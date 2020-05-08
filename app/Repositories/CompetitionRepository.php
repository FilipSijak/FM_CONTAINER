<?php

namespace App\Repositories;

use App\Models\Competition\Competition;
use App\Models\Game\BaseClubs;

class CompetitionRepository
{
    public function __construct()
    {

    }

    public function getBaseClubsByCompetition(Competition $competition)
    {
        return BaseClubs::all()->where('competition_id', $competition->id);
    }
}