<?php

namespace Services\CompetitionService\CompetitionUpdate;

use App\Repositories\CompetitionRepository;
use Illuminate\Support\Facades\DB;

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
        $competitionId       = $this->matches[0]['competition_id'];
        $tournamentStructure = $this->competitionRepository->tournamentKnockoutStageByCompetitionId($competitionId)[0];
        $summary             = json_decode($tournamentStructure->summary);

        $firsGroupRounds   = (array)$summary->first_group->rounds;
        $secondGroupRounds = (array)$summary->second_group->rounds;


        $this->updateTournamentGroup($firsGroupRounds);
        $this->updateTournamentGroup($secondGroupRounds);

        $summary->first_group->rounds  = $this->updateTournamentGroup($firsGroupRounds);
        $summary->second_group->rounds = $this->updateTournamentGroup($secondGroupRounds);

        $this->competitionRepository->updateKnockoutSummary($summary, $tournamentStructure->id);

        return $this;
    }

    private function updateTournamentGroup(array $tournamentGroup)
    {
        foreach ($this->matches as $match) {
            $matchesMapped[$match['id']] = $match;
        }

        foreach ($tournamentGroup as $round) {
            if (!empty($round)) {
                foreach ($round->pairs as $pair) {
                    if (!$pair->winner) {
                        if (isset($matchesMapped[$pair->match1Id]) && isset($matchesMapped[$pair->match2Id])) {
                            $winner = $this->competitionRepository->tournamentRoundWinner($pair->match1Id, $pair->match2Id);

                            $pair->winner = $winner;

                            // if both matches have been played, update the tournament and set the winner
                            // create a tournament match function in competition repository which would check who scored more goals to progress through
                        } else {
                            // means that only one match has been played in each round so no need to go through all of them
                            // this will change once matches are played on different dates
                            break;
                        }

                    } else {
                        break;
                    }
                }
            }
        }

        return $tournamentGroup;
    }
}
