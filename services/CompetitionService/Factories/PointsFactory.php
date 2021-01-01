<?php

namespace Services\CompetitionService\Factories;

use App\Models\Competition\CompetitionPoints;

class PointsFactory
{
    /**
     * @param int $clubId
     * @param int $competitionId
     * @param int $seasonId
     *
     * @return CompetitionPoints
     */
    public function make
    (
        int $clubId,
        int $competitionId,
        int $seasonId
    ): CompetitionPoints {
        $points                 = new CompetitionPoints();
        $points->club_id        = $clubId;
        $points->points         = 0;
        $points->competition_id = $competitionId;
        $points->season_id      = $seasonId;

        $points->save();

        return $points;
    }
}