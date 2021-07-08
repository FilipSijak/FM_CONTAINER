<?php

namespace App\GameEngine\GameCreation;

use App\Factories\Club\BalanceFactory;
use App\Factories\Game\GameFactory;
use App\GameEngine\Interfaces\CreateGameInterface;
use App\Models\Club\Club;
use App\Models\Competition\Competition;
use App\Models\Game\BaseCities;
use App\Models\Game\BaseClubs;
use App\Models\Game\BaseCompetitions;
use App\Models\Game\BaseCountries;
use App\Models\Game\BaseStadium;
use App\Models\Player\Player;
use App\Repositories\ClubRepository;
use App\Repositories\CompetitionRepository;
use App\Repositories\Player\PlayerRepository;
use Carbon\Carbon;
use Services\ClubService\GeneratePeople\InitialClubPeoplePotential;
use Services\CompetitionService\CompetitionsConfig\TournamentConfig;
use Services\CompetitionService\CompetitionService;
use Services\CompetitionService\Factories\SeasonFactory;
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
     * @var Carbon|\Carbon\CarbonImmutable
     */
    private $firstSeasonStartDate;

    /**
     * CreateGame constructor.
     *
     * @param int $userId
     */
    public function __construct(int $userId)
    {
        $this->userId                         = $userId;
        $this->firstSeasonFirstRoundStartDate = Carbon::create((int)date("Y"), 8, 15);
        $this->firstSeasonStartDate           = Carbon::create((int)date("Y"), 8, 15);
        $this->competitionService             = new CompetitionService();
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
             ->assignSeasonToGame($seasonFactory)
             ->assignCompetitionsToSeason()
             ->assignPlayersToClubs($peopleService)
             ->assignClubStaff($peopleService)
             ->assignBalancesToClubs();
    }

    /**
     * @return $this
     */
    private function assignBalancesToClubs(): CreateGame
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
    private function assignClubStaff(PeopleServiceInterface $peopleService): CreateGame
    {
        $clubPeoplePotential = new InitialClubPeoplePotential();

        foreach ($this->clubs as $club) {
            $staffMembers = $clubPeoplePotential->getStaffPotentialAndRole($club->rank);

            foreach ($staffMembers as $member) {
                if ($member->role == PersonTypes::MANAGER) {
                    $manager = $peopleService->setPersonConfiguration($member, $this->gameId, PersonTypes::MANAGER)
                                             ->createPerson();
                }
            }


            $manager->clubs()->attach($club->id);
        }

        return $this;
    }

    /**
     * @param PeopleServiceInterface $peopleService
     *
     * @return $this
     */
    private function assignPlayersToClubs(PeopleServiceInterface $peopleService): CreateGame
    {
        $clubPeoplePotential = new InitialClubPeoplePotential();
        $playerRepository    = new PlayerRepository();
        $clubRepository      = new ClubRepository();

        foreach ($this->clubs as $key => $club) {
            $playerList = $clubPeoplePotential->getPlayerPotentialAndInitialPosition($club->rank);
            $generatedPlayers    = [];
            $league              = $clubRepository->getLeagueByClub($club->id, 1);

            foreach ($playerList as $player) {
                $player = $peopleService->setPersonConfiguration($player, $this->gameId, PersonTypes::PLAYER)
                                        ->createPerson($club->rank, $league["rank"]);

                $generatedPlayers[] = $player;
            }

            $playerRepository->bulkPlayerInsert($this->gameId, $club->id, $generatedPlayers);
            $players = Player::where('club_id', $club->id)->get();
            $playerRepository->bulkAssignmentPlayersPositions($players);
        }

        return $this;
    }

    /**
     * @return $this
     */
    private function assignCompetitionsToSeason(): CreateGame
    {
        $competitions          = Competition::all();
        $competitionRepository = new CompetitionRepository();
        $tournamentConfig      = new TournamentConfig();
        $seasonStartDate       = $tournamentConfig->getStartDate()->format('Y-m-d');

        foreach ($competitions as $competition) {
            if ($competition->type == 'league' || ($competition->type == 'tournament' && $competition->groups)) {
                if ($competition->type == 'league') {
                    $clubs = $competitionRepository->getBaseClubsByCompetition($competition->id);
                    $this->competitionService->makeLeague($clubs, $competition->id, $this->season->id, $seasonStartDate);
                } else {
                    $clubs = $competitionRepository->getInitialTournamentTeamsBasedOnRanks($competition->id);
                    $this->competitionService->makeTournamentGroupStage($clubs, $competition->id, $this->season->id, $seasonStartDate);
                }
            } else {
                $clubs = $competitionRepository->getInitialTournamentTeamsBasedOnRanks($competition->id);
                $this->competitionService->makeTournament($clubs, $competition->id, $this->season->id, $seasonStartDate);
            }
        }

        return $this;
    }

    /**
     * @param SeasonFactory $seasonFactory
     *
     * @return $this
     */
    private function assignSeasonToGame(SeasonFactory $seasonFactory): CreateGame
    {
        $firstSeasonStartEndDate = $this->firstSeasonStartDate->copy()->add('1 year');

        $this->season = $seasonFactory->make($this->gameId, $this->firstSeasonStartDate, $firstSeasonStartEndDate);

        $this->season->save();

        return $this;
    }

    /**
     * @return $this
     */
    private function setAllClubs(): CreateGame
    {
        $this->clubs = Club::all();

        return $this;
    }

    /**
     * @param GameInitialDataSeedInterface $gameInitialDataSeed
     *
     * @return $this
     */
    private function populateFromBaseTables(GameInitialDataSeedInterface $gameInitialDataSeed): CreateGame
    {
        $gameInitialDataSeed->seedFromBaseTables($this->gameId);

        return $this;
    }

    /**
     * @return $this
     */
    private function storeGame(): CreateGame
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
    public function setClub(int $clubId): CreateGame
    {
        $this->clubId = $clubId;

        return $this;
    }
}
