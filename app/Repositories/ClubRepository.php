<?php

namespace App\Repositories;

use App\Models\Club\Club;
use App\Repositories\Interfaces\ClubRepositoryInterface;

class ClubRepository implements ClubRepositoryInterface
{
    public function getAllClubsByGame(int $gameId)
    {
        return Club::where('game_id', $gameId);
    }

    public function getInjuryList($gameId, $clubId)
    {

    }

    public function getRetiringPlayers($gameId, $clubId)
    {
        return [];
    }

    public function getActiveTransferBids()
    {

    }
}