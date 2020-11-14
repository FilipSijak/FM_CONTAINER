<?php

namespace Services\CompetitionService\CompetitionUpdate;

use App\Repositories\CompetitionRepository;

class LeagueUpdater
{
    protected $matches;
    /**
     * @var CompetitionRepository
     */
    private $competitionRepository;

    public function __construct()
    {
        $this->competitionRepository = new CompetitionRepository();
    }

    public function setMatches(array $matches)
    {
        $this->matches = $matches;

        return $this;
    }

    public function updatePointsTable()
    {
        foreach ($this->matches as $match) {
            $this->competitionRepository->updateCompetitionPoints($match);
        }

        return $this;
    }
}
