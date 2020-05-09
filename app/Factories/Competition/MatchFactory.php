<?php

namespace App\Factories\Competition;

use App\Models\Competition\Match;
use Nexmo\Client\Exception\Exception;

class MatchFactory
{
    /**
     * @param int    $gameId
     * @param int    $competitionId
     * @param int    $homeTeamId
     * @param int    $awayTeamId
     */
    public function make
    (
        int $gameId,
        int $competitionId,
        int $homeTeamId,
        int $awayTeamId
    ) {
        $match = new Match();

        $match->game_id        = $gameId;
        $match->competition_id = $competitionId;
        $match->hometeam_id    = $homeTeamId;
        $match->awayteam_id    = $awayTeamId;
        $match->stadium_id     = 0;
        $match->attendance     = 0;

        $match->save();
    }
}