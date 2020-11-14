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

        $match->save();
    }
}
