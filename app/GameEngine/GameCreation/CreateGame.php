<?php

namespace App\GameEngine\GameCreation;

use App\Factories\Club\BalanceFactory;
use Services\CompetitionService\Factories\SeasonFactory;
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
        $competitions          = Competition::all();
        $competitionRepository = new CompetitionRepository();

        foreach ($competitions as $competition) {
            if ($competition->type == 'league' || ($competition->type == 'tournament' && $competition->groups)) {
                if ($competition->type == 'league') {
                    $clubs = $competitionRepository->getBaseClubsByCompetition($competition->id);
                    $this->competitionService->makeLeague($clubs, $competition->id, $this->season->id);
                } else {
                    $clubs = $competitionRepository->getInitialTournamentTeamsBasedOnRanks($competition->id);
                    $this->competitionService->makeTournamentGroupStage($clubs, $competition->id, $this->season->id);
                }
            } else {
                $clubs = $competitionRepository->getInitialTournamentTeamsBasedOnRanks($competition->id);
                $this->competitionService->makeTournament($clubs, $competition->id, $this->season->id);
            }
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
