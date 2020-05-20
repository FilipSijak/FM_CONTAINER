<?php

namespace App\Repositories;

use App\Models\Competition\Competition;
use App\Models\Competition\Match;
use App\Models\Game\BaseClubs;
use App\Models\Game\Game;

class CompetitionRepository
{
    public function __construct()
    {

    }

    public function getBaseClubsByCompetition(Competition $competition)
    {
        return BaseClubs::all()->where('competition_id', $competition->id);
    }

    public function getScheduledGames(Game $game)
    {
        return Match::where('match_start', $game->game_date)->get();
    }

    public function getScheduledGamesForCompetition(Game $game, int $competitionId)
    {
        return Match::where('match_start', $game->game_date)->where('competition_id', $competitionId)->get();
    }
}