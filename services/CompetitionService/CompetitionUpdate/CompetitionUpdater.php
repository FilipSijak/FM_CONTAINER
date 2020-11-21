<?php

namespace Services\CompetitionService\CompetitionUpdate;

use App\Models\Competition\Competition;
use Illuminate\Support\Facades\DB;

class CompetitionUpdater
{
    /**
     * @var array
     */
    private $competitionMatches;

    public function setMatches(array $competitionMatches)
    {
        $this->competitionMatches = $competitionMatches;
    }

    /**
     * Each match will be checked where it belongs, and competitions will be updated based on the result of the match
     */
    public function distributeMatchesForCompetitionsUpdates()
    {
        $leagueUpdater = new LeagueUpdater();
        $tournamentUpdater = new TournamentUpdater();

        foreach ($this->competitionMatches as $competitionId => $matches) {
            $competition = Competition::find($competitionId);
            if ($competition->type == 'league') {
                $leagueUpdater->setMatches($matches)->updatePointsTable();
            } elseif ($competition->type == 'tournament') {
                if ($competition->groups) {

                    //$tournamentUpdater->setMatches($matches)->updatePointsTable();
                    // update group tables
                    // if group tables are finished, update competition summary
                } else {
                    // get all the matches for competition
                    $matches = DB::select("SELECT * FROM matches WHERE competition_id = :competitionId and winner > 0"
                    , ['competitionId' => $competitionId]);

                    $matches = json_decode(json_encode($matches), true);

                    $tournamentUpdater->setMatches($matches)->updateTournamentSummary();
                }

            }
            // call a function that will check which type of competition parser should the match go to
        }
    }
}
