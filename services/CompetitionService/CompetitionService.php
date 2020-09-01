<?php

namespace Services\CompetitionService;

use Services\CompetitionService\Interfaces\CompetitionServiceInterface;
use Services\CompetitionService\League\League;

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
        // TODO: Implement makeTournament() method.
    }
}