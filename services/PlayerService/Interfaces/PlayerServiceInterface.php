<?php

namespace Services\PlayerService\Interfaces;

interface PlayerServiceInterface
{
    public function createPlayer();

    public function setPlayerPotential(int $playerPotential);
}