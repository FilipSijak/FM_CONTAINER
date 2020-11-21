<?php

namespace Services\MatchService;

use App\Models\Competition\Match;
use Illuminate\Database\Eloquent\Collection;
use Services\MatchService\Interfaces\MatchServiceInterface;

class MatchService implements MatchServiceInterface
{
    public function simulateRound(Collection $matches)
    {
        // TODO: Implement simulateRound() method.
        foreach ($matches as $match) {
            $this->simulateMatch($match);
        }
    }

    private function simulateMatch(Match $match)
    {
        // check if the match is for a view mode (club that is managed by person will have view mode)

        // else, dummy simulation for all the other games
        // Class that will compare 2 clubs with all the players
        // calculate game based only on raw players attributes for now

        $playerAttributeSimulator = new PlayersAttributesSimulator();

        // cast to string, [0,1,2,3] will take 1 as a first number from in it (0)
        $match->winner = (string) rand(1, 3);

        $this->setMatchGoals($match);
        $match->save();
    }

    public function simulateMatchExtraTime(int $matchId)
    {
        $match = Match::find($matchId);

        // currently, penalties can't happen because winner is forced
        $match->winner = (string) rand(1, 2);

        $this->setMatchGoals($match);
        $match->save();

        // should return match winner team id instead of 1 / 2
        return $match->winner == 1 ? $match->hometeam_id : $match->awayteam_id;
    }

    private function setMatchGoals(&$match)
    {
        $winnerGoals = rand(1, 5);

        switch ($match->winner) {
            case 1:
                // home team win
                $match->home_team_goals = $winnerGoals;
                $match->away_team_goals = rand(0, $winnerGoals - 1);
                break;
            case 2:
                // away team win
                $match->away_team_goals = $winnerGoals;
                $match->home_team_goals = rand(0, $winnerGoals - 1);

                break;
            case 3:
                // draw
                $goals = rand(0, 3);

                $match->away_team_goals = $goals;
                $match->home_team_goals = $goals;
        }
    }
}
