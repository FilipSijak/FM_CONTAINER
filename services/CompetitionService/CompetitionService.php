<?php

namespace Services\CompetitionService;

use Illuminate\Database\Eloquent\Collection;
use Services\CompetitionService\Interfaces\CompetitionServiceInterface;
use Services\CompetitionService\League\League;

class CompetitionService implements CompetitionServiceInterface
{
    /**
     * @var Collection
     */
    protected $clubs;

    /**
     * CompetitionService constructor.
     *
     * @param Collection $clubs
     */
    public function __construct(Collection $clubs)
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