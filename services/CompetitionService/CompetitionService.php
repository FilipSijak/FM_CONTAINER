<?php

namespace Services\CompetitionService;

use App\Models\Club\Club;
use Services\CompetitionService\League\CreateLeague;

class CompetitionService
{
    public function makeLeague()
    {
        $clubs = Club::all();

        $createLeague = new CreateLeague($clubs);
    }
}