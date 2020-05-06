<?php

namespace App\GameEngine\Interfaces;

use App\Factories\Competition\SeasonFactory;
use Services\PlayerService\Interfaces\PlayerServiceInterface;

interface CreateGameInterface
{
    public function startNewGame();

    public function setAllClubs();

    public function assignPlayersToClubs(PlayerServiceInterface $playerService);

    public function assignBalancesToClubs();

    public function assignSeasonToGame(SeasonFactory $seasonFactory);
}