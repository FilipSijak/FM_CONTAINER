<?php

namespace App\Repositories\Match;

use App\Models\Club\Club;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MatchRepository
{
    /**
     * @param array  $leagueFixtures
     * @param int    $competitionId
     * @param Carbon $startDate
     * @param int    $roundLength
     */
    public function bulkInsertLeagueMatches(array $leagueFixtures, int $competitionId, Carbon $startDate, int $roundLength)
    {
        if (empty($leagueFixtures)) {
            return;
        }

        $countRound   = $roundLength;
        $insertString = "INSERT INTO matches(competition_id, hometeam_id, awayteam_id, stadium_id, match_start) VALUES";

        foreach ($leagueFixtures as $fixture) {
            $nextWeek   = $countRound % $roundLength == 0;
            $matchStart = $nextWeek ? $startDate->addWeek() : $startDate;

            $insertString .= "(" . $competitionId . "," . $fixture->homeTeamId . "," . $fixture->awayTeamId . "," . Club::where('id', $fixture->homeTeamId)->first()->stadium_id . ",'" . $matchStart->format("Y-m-d H:i:s") . "'), ";

            $countRound++;
        }

        $insert = substr($insertString, 0, -2);

        DB::statement($insert);
    }
}
