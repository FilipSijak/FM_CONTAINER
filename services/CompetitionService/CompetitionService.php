<?php

namespace Services\CompetitionService;


use Services\CompetitionService\CompetitionUpdate\CompetitionUpdater;
use Services\CompetitionService\Interfaces\CompetitionServiceInterface;
use Services\CompetitionService\League\League;
use Services\CompetitionService\Tournament\Tournament;

class CompetitionService implements CompetitionServiceInterface
{
    /**
     * @var array
     */
    protected $clubs;

    public function setClubs(array $clubs)
    {
        $this->clubs = $clubs;

        return $this;
    }

    public function makeLeague()
    {
        $league = new League($this->clubs);

        return $league->generateLeagueGames();
    }

    public function makeTournament()
    {
        $tournament = new Tournament($this->clubs);

        return $tournament->createTournament();
    }

    public function tournamentNewRound()
    {
        $tournament = new Tournament($this->clubs);

        return $tournament->setNextRoundPairs($this->clubs);
    }

    public function competitionsRoundUpdate(array $matches)
    {
        $competitionUpdater = new CompetitionUpdater();

        $competitionUpdater->setMatches($matches);
        $competitionUpdater->distributeMatchesForCompetitionsUpdates();
    }
}