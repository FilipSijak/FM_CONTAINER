<?php

namespace Services\CompetitionService\CompetitionUpdate;

use App\Repositories\CompetitionRepository;

class TournamentUpdater
{
    /**
     * @var array
     */
    private $matches;

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
        if ($this->competitionRepository->tournamentGroupsFinished($this->matches[0])) {
            return $this->updateTournamentSummary();
        }

        // update group tables

        return $this;
    }

    public function updateTournamentSummary()
    {


        return $this;
    }
}
