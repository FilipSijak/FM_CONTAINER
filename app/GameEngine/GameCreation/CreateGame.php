<?php

namespace App\GameEngine\GameCreation;

use App\Factories\Club\BalanceFactory;
use App\Factories\Competition\SeasonFactory;
use App\Factories\Game\GameFactory;
use App\Factories\Player\PlayerFactory;
use App\GameEngine\Interfaces\CreateGameInterface;
use App\Models\Club\Club;
use Services\ClubService\GeneratePeople\InitialClubPeoplePotential;
use Services\PlayerService\Interfaces\PlayerServiceInterface;

class CreateGame implements CreateGameInterface
{
    protected $newGame;

    protected $gameId;

    protected $userId;

    protected $clubId;

    protected $clubs;

    protected $season;

    /**
     * CreateGame constructor.
     *
     * @param int $userId
     */
    public function __construct(int $userId)
    {
        $this->userId = $userId;

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
     * @return $this
     */
    public function startNewGame()
    {
        $gameFactory = new GameFactory();

        $this->newGame = $gameFactory->setNewGame($this->userId);

        $this->gameId = $this->newGame->id;

        return $this;
    }

    /**
     * @return $this
     */
    public function setAllClubs()
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
     * @param PlayerServiceInterface $playerService
     *
     * @return $this
     */
    public function assignPlayersToClubs(PlayerServiceInterface $playerService)
    {
        $initialPlayerCreation = new InitialClubPeoplePotential();
        $playerFactory         = new PlayerFactory();

        foreach ($this->clubs as $club) {
            $playerPotentialList = $initialPlayerCreation->getPlayerPotentialListByClubRank($club->rank);

            foreach ($playerPotentialList as $playerPotential) {
                // returns generated player profile based on potential
                $servicePlayer = $playerService->setPlayerPotential($playerPotential)->createPlayer();
                // storing that player in database
                $player = $playerFactory->make($servicePlayer, $this->gameId);

                $player->clubs()->attach($club->id);
            }
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function assignBalancesToClubs()
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
    public function assignSeasonToGame(SeasonFactory $seasonFactory)
    {
        $this->season = $seasonFactory->make($this->gameId);

        $this->season->save();

        return $this;
    }

    public function assignCompetitionsToSeason()
    {

    }
}
