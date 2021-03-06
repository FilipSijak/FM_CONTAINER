<?php

namespace App\Repositories;

use App\Models\Competition\Season;
use App\Repositories\Interfaces\SeasonRepositoryInterface;

class SeasonRepository implements SeasonRepositoryInterface
{
    public function getAllSeasonsByGame(int $gameId)
    {
        return Season::all()->where('game_id', $gameId);
    }

    public function getCurrentSeasonByGameId(int $gameId)
    {
        return Season::where('game_id', $gameId)->get()->last();
    }
}