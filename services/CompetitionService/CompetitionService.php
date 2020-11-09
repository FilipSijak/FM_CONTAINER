<?php

namespace Services\CompetitionService;

use Services\CompetitionService\Interfaces\CompetitionServiceInterface;
use Services\CompetitionService\League\League;
use Services\CompetitionService\Tournament\Tournament;
use Illuminate\Database\Eloquent\Collection;

class CompetitionService implements CompetitionServiceInterface
{
    /**
     * @var array
     */
    protected $clubs;

    /**
     * CompetitionService constructor.
     *
     * @param array $clubs
     */
    public function __construct(array $clubs)
    {
        $this->clubs = $clubs;
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
}