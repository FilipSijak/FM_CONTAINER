<?php

namespace Services\CompetitionService\Factories;

use App\Models\Competition\Season;

class SeasonFactory
{
    public function make(int $gameId): Season
    {
        $season = new Season();

        $season->game_id = $gameId;
        $season->start_date = date('Y-m-d', strtotime(date("Y") . '-06-01'));
        $season->end_date = date('Y-m-d', strtotime('+1 year', strtotime($season->start_date)));

        return $season;
    }
}