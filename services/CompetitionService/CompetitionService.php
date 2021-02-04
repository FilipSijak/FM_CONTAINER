<?php

namespace Services\CompetitionService;


use Services\CompetitionService\CompetitionUpdate\CompetitionUpdater;
use Services\CompetitionService\Interfaces\CompetitionServiceInterface;
use Services\CompetitionService\League\League;
use Services\CompetitionService\Tournament\Tournament;

class CompetitionService implements CompetitionServiceInterface
{
    /**
     * @param array  $clubs
     * @param int    $competitionId
     * @param int    $seasonId
     * @param string $date
     */
    public function makeLeague(array $clubs, int $competitionId, int $seasonId, string $date)
    {
        $league = new League($clubs, $competitionId, $seasonId);

        $league->setLeagueCompetition($date);
    }

    /**
     * @param array  $clubs
     * @param int    $competitionId
     * @param int    $seasonId
     * @param string $date
     */
    public function makeTournament(array $clubs, int $competitionId, int $seasonId, string $date)
    {
        $tournament = new Tournament($clubs);

        $tournament->createTournament()
                   ->populateTournamentFixtures($competitionId, $date)
                   ->assignSeason($seasonId, $competitionId);
    }

    /**
     * @param array  $clubs
     * @param int    $competitionId
     * @param int    $seasonId
     * @param string $date
     */
    public function makeTournamentGroupStage(array $clubs, int $competitionId, int $seasonId, string $date)
    {
        $tournament = new Tournament($clubs);

        $tournament->createTournamentGroups($clubs, $competitionId, $seasonId, $date);
    }

    /**
     * @param array $clubs
     *
     * @return array
     */
    public function tournamentNewRound(array $clubs)
    {
        $tournament = new Tournament($clubs);

        return $tournament->setNextRoundPairs();
    }

    /**
     * @param array $matches
     */
    public function competitionsRoundUpdate(array $matches)
    {
        $competitionUpdater = new CompetitionUpdater();

        $competitionUpdater->setMatches($matches);
        $competitionUpdater->distributeMatchesForCompetitionsUpdates();
    }
}