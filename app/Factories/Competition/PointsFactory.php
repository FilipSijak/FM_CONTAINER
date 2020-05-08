<?php

namespace App\Factories\Competition;

use App\Models\Competition\CompetitionPoints;

class PointsFactory
{
    /**
     * @param int $clubId
     * @param int $gameId
     * @param int $competitionId
     * @param int $seasonId
     *
     * @return CompetitionPoints
     */
    public function make
    (
        int $clubId,
        int $gameId,
        int $competitionId,
        int $seasonId
    ) {
        $points                 = new CompetitionPoints();
        $points->club_id        = $clubId;
        $points->game_id        = $gameId;
        $points->points         = 0;
        $points->competition_id = $competitionId;
        $points->season_id      = $seasonId;

        $points->save();

        return $points;
    }
}