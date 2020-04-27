<?php

namespace Services\ClubService\GeneratePeople;

/**
 * Class InitialPlayerCreation
 *
 * @package Services\ClubService\GeneratePeople
 */
class InitialClubPeoplePotential
{
    protected const PLAYER_COUNT = 25;

    protected const AVERAGE_PLAYERS_COUNT = 15;

    protected const SPECIAL_PLAYERS_COUNT = 5;

    /**
     * Random player potential coming from club's rank
     * 3 sets of players: average, below and special player potentials based on club rank
     *
     * @param int $rank
     *
     * @return array
     */
    public function getPlayerPotentialListByClubRank(int $rank)
    {
        $playerPotentialList = [];
        $counter             = 1;
        $rank                = $rank * 10;

        while ($counter <= self::PLAYER_COUNT) {
            if ($counter <= self::AVERAGE_PLAYERS_COUNT) {
                // gets average potential +-5 from club's rank
                $playerPotentialList[] = rand($rank - 5, $rank + 5);
            } elseif ($counter <= self::AVERAGE_PLAYERS_COUNT + self::SPECIAL_PLAYERS_COUNT) {
                // gets lower player potential from rank
                $playerPotentialList[] = rand($rank - 20, $rank);
            } else {
                // gets player potential between club's rank and maximum potential (200)
                $playerPotentialList[] = rand($rank, 200);
            }

            $counter++;
        }

        return $playerPotentialList;
    }
}
