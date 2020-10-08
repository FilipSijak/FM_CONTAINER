<?php

namespace Services\PeopleService\PlayerPotential;

class PlayerPotential
{
    const POTENTIAL_BOUNDARIES = [0, 50, 75, 100, 130, 160, 180, 200];

    /**
     * Creates random value for technical, mental and physical potential
     *
     * @param int $coefficient
     *
     * @return \stdClass
     */
    public function calculatePlayerPotential(int $coefficient)
    {
        $playerPotential            = new \stdClass();
        $playerAttributesCategories = ['technical', 'mental', 'physical'];
        $potentialValue             = 0;

        for ($i = 0; $i < 3; $i++) {
            if (in_array($coefficient, self::POTENTIAL_BOUNDARIES)) {
                $playerPotential->{$playerAttributesCategories[$i]} = $coefficient;
                continue;
            }

            for ($k = 1; $k < count(self::POTENTIAL_BOUNDARIES); $k++) {
                if ($coefficient < self::POTENTIAL_BOUNDARIES[$k] && $coefficient > self::POTENTIAL_BOUNDARIES[$k - 1]) {
                    $potentialValue = rand(self::POTENTIAL_BOUNDARIES[$k - 1], self::POTENTIAL_BOUNDARIES[$k]);
                }
            }

            $playerPotential->{$playerAttributesCategories[$i]} = $potentialValue;
        }

        return $playerPotential;
    }

    /**
     * @param int $potential
     *
     * @return mixed
     */
    public function playerPotentialLabel(int $potential)
    {
        $labels = [
            'amateur'      => 50,
            'low'          => 75,
            'professional' => 100,
            'normal'       => 130,
            'high'         => 160,
            'very_high'    => 180,
            'world_class'  => 200,
        ];

        foreach ($labels as $labelCoefficient) {
            if ($potential <= $labelCoefficient) {
                return array_flip($labels)[$labelCoefficient];
            }
        }
    }
}
