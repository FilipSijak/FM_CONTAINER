<?php

namespace Services\PeopleService\Regens\PlayerRegen;

use Services\PeopleService\PersonConfig\Player\PlayerFields;
use Services\PeopleService\PersonConfig\Player\PlayerPositionConfig;
use Services\PeopleService\PersonPotential\PersonPotential;

/**
 * Class PlayerInitialAttributes
 *
 * @package Services\PlayerService\PlayerCreation
 */
class InitialAttributes
{
    protected $playerPosition;
    protected $playerAllAttributes = [];
    protected $commonAttributes    = ['stamina', 'acceleration', 'strength'];
    protected $playerPotentialByCategory;

    public function __construct(
        array $playerPotentialByCategory,
        string $playerPosition
    ) {
        $this->playerPosition            = $playerPosition;
        $this->playerPotentialByCategory = $playerPotentialByCategory;

        $this->setAttributes();
    }

    /**
     * @return array
     */
    public function getAllAttributeValues(): array
    {
        return $this->playerAllAttributes;
    }

    protected function setAttributes()
    {
        $mainAttributes = PlayerPositionConfig::getPositionMainAttributes($this->playerPosition);

        foreach ($mainAttributes as $attributesCategory => $importanceList) {
            $this->setPrimaryAttributes($importanceList['primary'], $attributesCategory);
            $this->setSecondaryAttributes($importanceList['secondary'], $attributesCategory);
            $this->setOtherAttributes();
        }
    }

    /**
     * This will set attributes for a specific category (e.g. technical)
     * Goes through each primary attribute and gives it a random value from a higher range
     *
     * @param array  $primaryAttributes
     * @param string $attributesCategory
     */
    protected function setPrimaryAttributes(array $primaryAttributes, string $attributesCategory)
    {
        foreach ($primaryAttributes as $attribute) {
            $this->playerAllAttributes[$attribute] = (int)round(
                rand($this->playerPotentialByCategory[$attributesCategory] - 15,
                     $this->playerPotentialByCategory[$attributesCategory]
                ) / 10
            );
        }
    }

    /**
     * Secondary attributes would be lower than main (on average) and higher than the rest
     * If a striker has finishing attribute of 20, his secondary attribute for that position (dribbling) will be lower
     * (15) but still higher than tackling (8)
     *
     * @param $attributes
     * @param $attributesCategory
     */
    protected function setSecondaryAttributes($attributes, $attributesCategory)
    {
        foreach ($attributes as $attribute) {
            $this->playerAllAttributes[$attribute] = (int)round(
                rand(
                    $this->playerPotentialByCategory[$attributesCategory] - 40,
                    $this->playerPotentialByCategory[$attributesCategory]
                ) / 10
            );
        }
    }

    /**
     * Sets the rest of the player attributes that weren't filled by primary or secondary run
     */
    protected function setOtherAttributes()
    {
        $attributeCategories = ['technical', 'mental', 'physical'];

        $allAbilityAttributes = array_merge(
            PlayerFields::TEHNICAL_FIELDS,
            PlayerFields::MENTAL_FIELDS,
            PlayerFields::PHYSICAL_FILDS
        );

        foreach ($allAbilityAttributes as $field) {
            foreach ($attributeCategories as $category) {
                // checks the object if the attribute was already set for primary or secondary value
                if (!isset($this->playerAllAttributes[$field])) {
                    $potentialForCategory  = $this->playerPotentialByCategory[$category];
                    $minimumAttributeValue = $this->setMinimumAttributeValue($potentialForCategory);

                    if (isset($this->commonAttributes[$field])) {
                        $minimumAttributeValue = $minimumAttributeValue + 3;
                    }

                    /*
                     * After getting a minimal value for an attribute, the value is used as a starting point for rand
                     * Example: Player with min value of 5 can end up with anything between 5 and $potentialForCategory
                     */
                    $this->playerAllAttributes[$field] = (int)round(rand($minimumAttributeValue, $potentialForCategory / 10));
                }
            }
        }
    }

    /**
     * @param int $playerPotential
     *
     * @return int
     */
    protected function setMinimumAttributeValue(int $playerPotential): int
    {
        $potentialDescription = PersonPotential::playerPotentialLabel($playerPotential);

        $potentialMinimumAttributesRanges = [
            'amateur'      => 3,
            'low'          => 4,
            'professional' => 5,
            'normal'       => 6,
            'high'         => 7,
            'very_high'    => 8,
            'world_class'  => 9,
        ];

        return $potentialMinimumAttributesRanges[$potentialDescription];
    }
}
