<?php

namespace Services\CompetitionService\CompetitionUpdate;

use App\Models\Competition\Competition;

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

                    $tournamentUpdater->setMatches($matches)->updatePointsTable();
                    // update group tables
                    // if group tables are finished, update competition summary
                } else {
                    // update json summary
                }

            }
            // call a function that will check which type of competition parser should the match go to
        }
    }
}