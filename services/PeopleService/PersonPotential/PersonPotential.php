<?php

namespace Services\PeopleService\PersonPotential;

class PersonPotential
{
    const POTENTIAL_BOUNDARIES = [0, 50, 75, 100, 130, 160, 180, 200];

    /**
     * @var array
     */
    protected $categories;

    public function setPersonCategories(array $categories)
    {
        $this->categories = $categories;
    }

    /**
     * Creates random value for technical, mental and physical potential based on the provided potential
     *
     * @param int $potential
     *
     * @return \stdClass
     */
    public function calculatePersonPotential(int $potential)
    {
        $personPotential            = new \stdClass();
        $potentialValue             = 0;
        $offset = count($this->categories);

        for ($i = 0; $i < $offset; $i++) {
            if (in_array($potential, self::POTENTIAL_BOUNDARIES)) {
                $personPotential->{$this->categories[$i]} = $potential;
                continue;
            }

            for ($k = 1; $k < count(self::POTENTIAL_BOUNDARIES); $k++) {
                if ($potential < self::POTENTIAL_BOUNDARIES[$k] && $potential > self::POTENTIAL_BOUNDARIES[$k - 1]) {
                    $potentialValue = rand(self::POTENTIAL_BOUNDARIES[$k - 1], self::POTENTIAL_BOUNDARIES[$k]);
                }
            }

            $personPotential->{$this->categories[$i]} = $potentialValue;
        }

        return $personPotential;
    }

    /**
     * @param int $potential
     *
     * @return string
     */
    public static function playerPotentialLabel(int $potential)
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

        foreach ($labels as $label => $labelCoefficient) {
            if ($potential <= $labelCoefficient) {
                return $label;
            }
        }
    }
}
