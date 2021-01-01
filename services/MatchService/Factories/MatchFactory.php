<?php

namespace Services\MatchService\Factories;

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
     *
     * @return Match
     */
    public function make
    (
        int $competitionId,
        int $homeTeamId,
        int $awayTeamId,
        Carbon $matchStart
    ): Match {
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