<?php

namespace App\Repositories\Interfaces;

interface SeasonRepositoryInterface
{
    public function getAllSeasonsByGame(int $gameId);
}