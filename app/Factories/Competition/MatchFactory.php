<?php

namespace App\Factories\Competition;

use App\Models\Club\Club;
use App\Models\Competition\Match;
use Carbon\Carbon;

class MatchFactory
{
    /**
     * @param int    $competitionId
     * @param int    $homeTeamId
     * @param int    $awayTeamId
     * @param Carbon $matchStart
     */
    public function make
    (
        int $competitionId,
        int $homeTeamId,
        int $awayTeamId,
        Carbon $matchStart
    ) {
        $match = new Match();

        $match->competition_id = $competitionId;
        $match->hometeam_id    = $homeTeamId;
        $match->awayteam_id    = $awayTeamId;
        $match->stadium_id     = Club::all()->where('id', $homeTeamId)->first()->stadium_id;
        $match->attendance     = 0;
        $match->match_start    = $matchStart;

        $match->save();

        return $match;
    }
}