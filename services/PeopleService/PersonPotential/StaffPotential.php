<?php

namespace Services\PeopleService\PersonPotential;

class StaffPotential
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
        $personPotential            = new \stdClass();
        $personAttributesCategories = ['coaching', 'mental', 'goalkeeping', 'knowledge'];
        $potentialValue             = 0;

        for ($i = 0; $i < 4; $i++) {
            if (in_array($coefficient, self::POTENTIAL_BOUNDARIES)) {
                $personPotential->{$personAttributesCategories[$i]} = $coefficient;
                continue;
            }

            for ($k = 1; $k < count(self::POTENTIAL_BOUNDARIES); $k++) {
                if ($coefficient < self::POTENTIAL_BOUNDARIES[$k] && $coefficient > self::POTENTIAL_BOUNDARIES[$k - 1]) {
                    $potentialValue = rand(self::POTENTIAL_BOUNDARIES[$k - 1], self::POTENTIAL_BOUNDARIES[$k]);
                }
            }

            $personPotential->{$personAttributesCategories[$i]} = $potentialValue;
        }

        return $personPotential;
    }
}
