<?php

namespace Services\CompetitionService\Tournament;

class Tournament
{
    /**
     * @var array
     */
    private $data;

    /**
     * Tournament constructor.
     *
     * @param $clubs
     */
    public function __construct($clubs)
    {
        $this->clubs = $clubs;
    }

    /**
     * @return array
     */
    public function createTournament()
    {
        $clubsCount = count($this->clubs);
        $halfSize   = ($clubsCount / 2);
        $rounds     = 0;

        $calcNumRounds = function ($n) use (&$calcNumRounds, &$rounds) {
            if ($n % 2 == 1) {
                return 1;
            }

            $rounds++;

            return $calcNumRounds($n / 2);
        };


        $calcNumRounds($halfSize);

        $data = [
            "first_group"       => [
                "num_rounds" => $rounds,
                "rounds"     => [],
            ],
            "second_group"      => [
                "num_rounds" => $rounds,
                "rounds"     => [],
            ],
            "winner"            => null,
            "second_placed"     => null,
            "third_placed"      => null,
            "finals_match"      => null,
            "third_place_match" => null,
        ];

        for ($i = 1; $i <= $rounds; $i++) {
            $data["first_group"]["rounds"][$i]  = ["pairs" => []];
            $data["second_group"]["rounds"][$i] = ["pairs" => []];
        }

        for ($i = 0, $k = $clubsCount - 1; $i < $halfSize; $i++, $k--) {
            $pair = $this->makePairMatches($this->clubs[$i]['id'], $this->clubs[$k]['id']);

            // half of the pairs go into one group, the other half into a second group
            if ($i < $halfSize / 2) {
                $data["first_group"]["rounds"][1]["pairs"][] = $pair;
            } else {
                $data["second_group"]["rounds"][1]["pairs"][] = $pair;
            }
        }

        return $data;
    }

    /**
     * @param int $firstTeamId
     * @param int $secondTeamId
     *
     * @return \stdClass
     */
    private function makePairMatches(int $firstTeamId, int $secondTeamId)
    {
        $pair   = new \stdClass();
        $match1 = new \stdClass();
        $match2 = new \stdClass();

        $match1->homeTeamId = $firstTeamId;
        $match1->awayTeamId = $secondTeamId;
        $match2->homeTeamId = $secondTeamId;
        $match2->awayTeamId = $firstTeamId;

        $pair->match1   = $match1;
        $pair->match2   = $match2;
        $pair->winner   = null;
        $pair->match1Id = null;
        $pair->match2Id = null;

        return $pair;
    }

    /**
     * @param array $clubs
     *
     * @return array
     */
    public function setNextRoundPairs(array $clubs)
    {
        $clubsCount = count($clubs);
        $halfSize   = ($clubsCount / 2);
        $pairs      = [];

        for ($i = 0, $k = $clubsCount - 1; $i < $halfSize; $i++, $k--) {
            $pairs[] = $this->makePairMatches($clubs[$i], $clubs[$k]);
        }

        return $pairs;
    }
}