<?php

namespace Services\CompetitionService\Tournament;

class Tournament
{
    /**
     * @var array
     */
    private $data;

    public function __construct($clubs)
    {
        $this->clubs = $clubs;
    }

    public function createTournament()
    {
        $clubsCount  = count($this->clubs);
        $halfSize    = ($clubsCount / 2);
        $groupRounds = $halfSize / 2;

        $data = [
            "first_group"   => [
                "num_rounds" => $groupRounds,
                "rounds"     => [],
            ],
            "second_group"  => [
                "num_rounds" => $groupRounds,
                "rounds"     => [],
            ],
            "winner"        => null,
            "second_placed" => null,
            "third_placed"  => null,
        ];

        for ($i = 1; $i <= $groupRounds; $i++) {
            $data["first_group"]["rounds"][$i]  = ["pairs" => []];
            $data["second_group"]["rounds"][$i] = ["pairs" => []];
        }

        for ($i = 0, $k = $clubsCount - 1; $i < $halfSize; $i++, $k--) {
            $pair   = new \stdClass();
            $match1 = new \stdClass();
            $match2 = new \stdClass();

            $match1->homeTeamId = $this->clubs[$i]['id'];
            $match1->awayTeamId = $this->clubs[$k]['id'];
            $match2->homeTeamId = $this->clubs[$k]['id'];
            $match2->awayTeamId = $this->clubs[$i]['id'];

            $pair->match1 = $match1;
            $pair->match2 = $match2;
            $pair->winner = null;
            $pair->loser  = null;
            $pair->match1Id = null;
            $pair->match2Id = null;

            if ($i < $groupRounds) {
                $data["first_group"]["rounds"][1]["pairs"][] = $pair;
            } else {
                $data["second_group"]["rounds"][1]["pairs"][] = $pair;
            }
        }

        return $data;
    }

    public function setExistingTournamentData(array $data)
    {
        $this->data = $data;

        return $this;
    }

}