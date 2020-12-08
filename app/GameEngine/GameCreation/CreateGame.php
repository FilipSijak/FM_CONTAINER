<?php

namespace App\GameEngine\GameCreation;

use App\Factories\Club\BalanceFactory;
use App\Factories\Competition\MatchFactory;
use App\Factories\Competition\PointsFactory;
use App\Factories\Competition\SeasonFactory;
use App\Factories\Game\GameFactory;
use App\GameEngine\Interfaces\CreateGameInterface;
use App\Models\Club\Club;
use App\Models\Competition\Competition;
use App\Models\Game\BaseCities;
use App\Models\Game\BaseClubs;
use App\Models\Game\BaseCompetitions;
use App\Models\Game\BaseCountries;
use App\Models\Game\BaseStadium;
use App\Repositories\CompetitionRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Services\ClubService\GeneratePeople\InitialClubPeoplePotential;
use Services\CompetitionService\CompetitionService;
use Services\GameService\GameData\GameInitialDataSeed;
use Services\GameService\Interfaces\GameInitialDataSeedInterface;
use Services\PeopleService\Interfaces\PeopleServiceInterface;
use Services\PeopleService\PeopleService;
use Services\PeopleService\PersonTypes;

class CreateGame implements CreateGameInterface
{
    protected $newGame;

    protected $gameId;

    protected $userId;

    protected $clubId;

    protected $clubs;

    protected $season;

    /**
     * @var Carbon|\DateTime|\DateTimeInterface
     */
    protected $firstSeasonFirstRoundStartDate;
    /**
     * @var CompetitionService
     */
    private $competitionService;

    /**
     * CreateGame constructor.
     *
     * @param int $userId
     */
    public function __construct(int $userId)
    {
        $this->userId                         = $userId;
        $this->firstSeasonFirstRoundStartDate = Carbon::create((int)date("Y"), 8, 15);
        $this->competitionService             = new CompetitionService();

        return $this;
    }

    /**
     * @return $this
     */
    public function getExistingGames()
    {
        return $this;
    }

    /**
     * @return void
     */
    public function startNewGame()
    {
        $peopleService    = new PeopleService();
        $seasonFactory    = new SeasonFactory();
        $baseClubs        = new BaseClubs();
        $baseCountries    = new BaseCountries();
        $baseCompetitions = new BaseCompetitions();
        $baseCities       = new BaseCities();
        $baseStadium      = new BaseStadium();

        $gameInitialDataSeed = new GameInitialDataSeed(
            $baseClubs, $baseCountries, $baseCompetitions, $baseCities, $baseStadium
        );

        $this->storeGame()
             ->populateFromBaseTables($gameInitialDataSeed)
             ->setAllClubs()
             ->assignPlayersToClubs($peopleService)
             ->assignClubStaff($peopleService)
             ->assignBalancesToClubs()
             ->assignSeasonToGame($seasonFactory)
             ->assignCompetitionsToSeason();
    }

    /**
     * @return $this
     */
    private function assignCompetitionsToSeason()
    {
        $competitions = Competition::all();

        foreach ($competitions as $competition) {
            if ($competition->type == 'league') {
                $this->setLeagueCompetition($competition);
            } else {
                $this->setTournamentCompetition($competition);
            }
        }

        return $this;
    }

    /**
     * @param Competition $competition
     */
    private function setLeagueCompetition(Competition $competition)
    {
        $pointsFactory         = new PointsFactory();
        $competitionRepository = new CompetitionRepository();
        $clubsByCompetition    = $competitionRepository->getBaseClubsByCompetition($competition->id);
        $leagueFixtures        = $this->competitionService->setClubs($clubsByCompetition->toArray())->makeLeague();
        $carbonCopy            = $this->firstSeasonFirstRoundStartDate->copy();
        $seasonStart           = $carbonCopy->modify("next Sunday");

        $this->populateLeagueFixtures($leagueFixtures, $competition->id, $seasonStart);

        foreach ($clubsByCompetition as $club) {
            $competition->seasons()->attach(
                $this->season->id,
                [
                    'game_id' => $this->gameId,
                    'club_id' => $club->id,
                ]
            );

            $pointsFactory->make(
                $club->id,
                $this->gameId,
                $competition->id,
                $this->season->id
            );
        }
    }

    /**
     * @param array $leagueFixtures
     * @param       $competitionId
     */
    private function populateLeagueFixtures(array $leagueFixtures, $competitionId, $startDate, $roundLength = 10)
    {
        $matchFactory = new MatchFactory();
        $countRound   = $roundLength;

        foreach ($leagueFixtures as $fixture) {
            $nextWeek = $countRound % $roundLength == 0;

            $matchFactory->make(
                $this->gameId,
                $competitionId,
                $fixture->homeTeamId,
                $fixture->awayTeamId,
                $nextWeek ? $startDate->addWeek() : $startDate
            );

            $countRound++;
        }
    }

    /**
     * @param Competition $competition
     */
    public function setTournamentCompetition(Competition $competition)
    {
        $competitionRepository = new CompetitionRepository();
        $clubsByCompetition    = $competitionRepository->getInitialTournamentTeamsBasedOnRanks($competition);
        $tournament            = $this->competitionService->setClubs($clubsByCompetition->toArray())->makeTournament();

        if ($competition->groups) {
            $this->populateTournamentGroups($competition->id);

            $mappedTeams = $competitionRepository->getTeamsMappedByTournamentGroup($competition->id);

            foreach ($mappedTeams as $group => $teams) {
                $carbonCopy     = $this->firstSeasonFirstRoundStartDate->copy();
                $firstRoundDate = $carbonCopy->modify("next Tuesday");
                $leagueFixtures = $this->competitionService->setClubs($teams)->makeLeague();

                $this->populateLeagueFixtures($leagueFixtures, $competition->id, $firstRoundDate, 2);
            }
        } else {
            $this->populateTournamentFixtures($tournament, $competition->id);
        }
    }

    /**
     * @param int $competitionId
     */
    public function populateTournamentGroups(int $competitionId)
    {
        $competitionRepository = new CompetitionRepository();
        $clubsByCompetition    = $competitionRepository->getInitialTournamentTeamsBasedOnRanks();
        $counter               = 0;
        $currentGroup          = '';

        $groups = [
            0  => 'A',
            4  => 'B',
            8  => 'C',
            12 => 'D',
            16 => 'E',
            20 => 'F',
        ];

        for ($i = 0; $i < count($clubsByCompetition); $i++) {
            if (isset($groups[$counter])) {
                $currentGroup = $groups[$counter];
            }

            try {
                DB::insert(
                    "
                    INSERT INTO tournament_groups (competition_id, groupId, club_id, points)
                    VALUES (:competitionId, :groupId, :clubId, :points)
                    ",
                    [
                        'competitionId' => $competitionId,
                        'groupId'       => $currentGroup,
                        'clubId'        => $clubsByCompetition[$i]->id,
                        'points'        => 0,
                    ]
                );
            } catch (\Exception $e) {
                // @TODO
            }

            $counter++;
        }
    }

    /**
     * @param array $tournament
     * @param       $competitionId
     */
    public function populateTournamentFixtures(array $tournament, $competitionId)
    {
        $matchFactory = new MatchFactory();
        $firstGame    = $this->firstSeasonFirstRoundStartDate->copy()->modify("next Tuesday");
        $secondGame   = $firstGame->copy()->addWeek();

        $firstRoundPairs = array_merge(
            $tournament["first_group"]["rounds"][1]["pairs"],
            $tournament["second_group"]["rounds"][1]["pairs"]
        );

        foreach ($firstRoundPairs as $pair) {
            $match1 = $matchFactory->make(
                $this->gameId,
                $competitionId,
                $pair->match1->homeTeamId,
                $pair->match1->awayTeamId,
                $firstGame
            );

            $pair->match1Id = $match1->id;

            $match2 = $matchFactory->make(
                $this->gameId,
                $competitionId,
                $pair->match2->homeTeamId,
                $pair->match2->awayTeamId,
                $secondGame
            );

            $pair->match2Id = $match2->id;
        }

        DB::insert(
            "
                INSERT INTO tournament_knockout (competition_id, summary)
                VALUES (:competitionId, :summary)
            ",
            [
                'competitionId' => $competitionId,
                'summary'       => json_encode($tournament),
            ]
        );
    }

    /**
     * @param SeasonFactory $seasonFactory
     *
     * @return $this
     */
    private function assignSeasonToGame(SeasonFactory $seasonFactory)
    {
        $this->season = $seasonFactory->make($this->gameId);

        $this->season->save();

        return $this;
    }

    /**
     * @return $this
     */
    private function assignBalancesToClubs()
    {
        $balanceFactory = new BalanceFactory();

        foreach ($this->clubs as $club) {
            $balanceFactory->make($club, $this->gameId);
        }

        return $this;
    }

    /**
     * Assign 1 manager, 5 scouts and 5 coaches for every club
     *
     * @param PeopleServiceInterface $peopleService
     *
     * @return CreateGame
     */
    private function assignClubStaff(PeopleServiceInterface $peopleService)
    {
        foreach ($this->clubs as $club) {
            $manager = $peopleService->setPersonConfiguration($club->rank, $this->gameId, PersonTypes::MANAGER)
                                     ->createPerson();

            $manager->clubs()->attach($club->id);
        }

        return $this;
    }

    /**
     * @param PeopleServiceInterface $peopleService
     *
     * @return $this
     */
    private function assignPlayersToClubs(PeopleServiceInterface $peopleService)
    {
        $clubPeoplePotential = new InitialClubPeoplePotential();

        foreach ($this->clubs as $club) {
            $playerPotentialList = $clubPeoplePotential->getPlayerPotentialListByClubRank($club->rank);

            foreach ($playerPotentialList as $playerPotential) {
                $player = $peopleService->setPersonConfiguration($playerPotential, $this->gameId, PersonTypes::PLAYER)
                                        ->createPerson();

                $player->clubs()->attach($club->id);
            }
        }

        return $this;
    }

    /**
     * @return $this
     */
    private function setAllClubs()
    {
        $this->clubs = Club::all();

        return $this;
    }

    /**
     * @param GameInitialDataSeedInterface $gameInitialDataSeed
     *
     * @return $this
     */
    private function populateFromBaseTables(GameInitialDataSeedInterface $gameInitialDataSeed)
    {
        $gameInitialDataSeed->seedFromBaseTables($this->gameId);

        return $this;
    }

    /**
     * @return $this
     */
    private function storeGame()
    {
        $gameFactory = new GameFactory();

        $this->newGame = $gameFactory->setNewGame($this->userId);

        $this->gameId = $this->newGame->id;

        return $this;
    }

    /**
     * @param int $clubId
     *
     * @return $this
     */
    public function setClub(int $clubId)
    {
        $this->clubId = $clubId;

        return $this;
    }
}
