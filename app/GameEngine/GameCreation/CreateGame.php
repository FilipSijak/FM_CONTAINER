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
use Services\ClubService\GeneratePeople\InitialClubPeoplePotential;
use Services\CompetitionService\CompetitionService;
use Services\GameService\GameData\GameInitialDataSeed;
use Services\GameService\Interfaces\GameInitialDataSeedInterface;
use Services\PeopleService\Interfaces\PeopleServiceInterface;
use Services\PeopleService\PersonCreate\Player;
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
     * CreateGame constructor.
     *
     * @param int $userId
     */
    public function __construct(int $userId)
    {
        $this->userId                         = $userId;
        $this->firstSeasonFirstRoundStartDate = Carbon::create((int)date("Y"), 8, 15);

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
            ->assignBalancesToClubs()
            ->assignSeasonToGame($seasonFactory)
            ->assignCompetitionsToSeason();
    }

    private function storeGame()
    {
        $gameFactory = new GameFactory();

        $this->newGame = $gameFactory->setNewGame($this->userId);

        $this->gameId = $this->newGame->id;

        return $this;
    }

    private function populateFromBaseTables(GameInitialDataSeedInterface $gameInitialDataSeed)
    {
        $gameInitialDataSeed->seedFromBaseTables($this->gameId);

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
     * @param int $clubId
     *
     * @return $this
     */
    public function setClub(int $clubId)
    {
        $this->clubId = $clubId;

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

    private function assignClubStaff()
    {
        foreach ($this->clubs as $club) {

        }
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

    private function assignCompetitionsToSeason()
    {
        $competitions          = Competition::all();
        $competitionRepository = new CompetitionRepository();
        $pointsFactory         = new PointsFactory();

        foreach ($competitions as $competition) {

            $clubsByCompetition = $competitionRepository->getBaseClubsByCompetition($competition);
            $competitionService = new CompetitionService($clubsByCompetition->toArray());
            $leagueFixtures     = $competitionService->makeLeague();

            $this->populateLeagueFixtures($leagueFixtures, $competition->id);

            foreach ($clubsByCompetition as $club) {
                $competition->seasons()->attach($this->season->id, ['game_id' => $this->gameId, 'club_id' => $club->id]);

                $pointsFactory->make(
                    $club->id,
                    $this->gameId,
                    $competition->id,
                    $this->season->id
                );
            }
        }
    }

    /**
     * @param array $leagueFixtures
     * @param       $competitionId
     */
    private function populateLeagueFixtures(array $leagueFixtures, $competitionId)
    {
        $matchFactory = new MatchFactory();
        $seasonStart  = $this->firstSeasonFirstRoundStartDate::parse()->modify("next Sunday");
        $countRound   = count($this->clubs) / 2;

        foreach ($leagueFixtures as $fixture) {
            $nextWeek = $countRound % 10 == 0;

            $matchFactory->make(
                $this->gameId,
                $competitionId,
                $fixture->homeTeamId,
                $fixture->awayTeamId,
                $nextWeek ? $seasonStart->addWeek(1) : $seasonStart
            );

            $countRound++;
        }
    }
}
