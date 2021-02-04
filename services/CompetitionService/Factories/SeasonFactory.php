<?php

namespace Services\CompetitionService\Factories;

use App\Models\Competition\Season;

class SeasonFactory
{
    public function make(int $gameId, string $startDate, string $endDate): Season
    {
        $season = new Season();

        $season->game_id    = $gameId;
        $season->start_date = $startDate;
        $season->end_date   = $endDate;

        return $season;
    }
}