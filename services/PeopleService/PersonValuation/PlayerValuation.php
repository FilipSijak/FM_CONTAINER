<?php

namespace Services\PeopleService\PersonValuation;

class PlayerValuation
{
    private $playerValue = 0;

    /**
     * @return int
     */
    public function getPlayerValue(): int
    {
        return $this->playerValue;
    }

    /**
     * @param int    $playerAveragePotential
     * @param int    $clubRank
     * @param int    $leagueRank
     * @param string $dob
     */
    public function setPersonValue(
        int $playerAveragePotential,
        int $clubRank,
        int $leagueRank,
        string $dob
    ) {
        /*
         * value = club rank * league rank * player potential * potentialMultiplier * ageMultiplier
         * gets increased/decreased based on age, potential multiplier, contract, transfer status, injury etc..
         */

        $age                  = date("Y", strtotime($dob));
        $ageCoefficient       = $this->ageCategories($age);
        $potentialCoefficient = $this->potentialCoefficient($playerAveragePotential);

        $this->playerValue = $clubRank * $leagueRank * $playerAveragePotential * $potentialCoefficient * $ageCoefficient;
    }

    /**
     * @param int $playerAge
     *
     * @return int|string
     */
    private function ageCategories(int $playerAge)
    {
        $playerAge = date("Y") - $playerAge;

        $groups = [
            "0.8"  => "17-19",
            "1.2"  => "19-28",
            "0.6"  => "29-31",
            "0.4"  => "31-33",
            "0.2"  => "33-35",
            "0.05" => "35-40",
        ];

        foreach ($groups as $ageMultiplier => $group) {
            $range = explode("-", $group);

            if ($range[0] <= $playerAge && $range[1] >= $playerAge) {
                return $ageMultiplier;
            }
        }

        return 1;
    }

    /**
     * @param int $playerPotential
     *
     * @return int|string
     */
    private function potentialCoefficient(int $playerPotential)
    {
        $potentialRange = [
            "0.03" => "40-49",
            "0.04" => "50-59",
            "0.05" => "60-69",
            "0.06" => "70-79",
            "0.07" => "80-89",
            "0.08" => "90-99",
            "0.09" => "100-109",
            "0.1"  => "110-119",
            "0.2"  => "120-129",
            "0.3"  => "130-139",
            "0.4"  => "140-149",
            "0.5"  => "150-159",
            "1"    => "160-169",
            "1.5"  => "170-179",
            "2"    => "180-189",
            "2.5"  => "190-200",
        ];

        foreach ($potentialRange as $potentialMultiplier => $range) {
            $rangeValues = explode("-", $range);

            if ($playerPotential >= $rangeValues[0] && $playerPotential <= $rangeValues[1]) {
                return $potentialMultiplier;
            }
        }

        return 1;
    }
}