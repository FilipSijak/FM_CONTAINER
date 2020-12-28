<?php

namespace Services\CompetitionService;


use Services\CompetitionService\CompetitionUpdate\CompetitionUpdater;
use Services\CompetitionService\Interfaces\CompetitionServiceInterface;
use Services\CompetitionService\League\League;
use Services\CompetitionService\Tournament\Tournament;

class CompetitionService implements CompetitionServiceInterface
{
    /**
     * @param array $clubs
     * @param int   $competitionId
     * @param int   $seasonId
     */
    public function makeLeague(array $clubs, int $competitionId, int $seasonId)
    {
        $league = new League($clubs, $competitionId, $seasonId);

        $league->setLeagueCompetition();
    }

    /**
     * @param array $clubs
     * @param int   $competitionId
     * @param int   $seasonId
     */
    public function makeTournament(array $clubs, int $competitionId, int $seasonId)
    {
        $tournament = new Tournament($clubs);

        $tournament->createTournament()->populateTournamentFixtures($competitionId);
    }

    /**
     * @param array $clubs
     * @param int   $competitionId
     * @param int   $seasonId
     */
    public function makeTournamentGroupStage(array $clubs, int $competitionId, int $seasonId)
    {
        $tournament = new Tournament($clubs);

        $tournament->setTournamentGroups($clubs, $competitionId, $seasonId);
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