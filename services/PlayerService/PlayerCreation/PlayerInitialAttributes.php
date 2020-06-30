<?php

namespace Services\PlayerService\PlayerCreation;

use Services\PlayerService\PlayerConfig\PlayerFields;
use Services\PlayerService\PlayerConfig\PlayerPositionConfig;
use Services\PlayerService\PlayerPotential\PlayerPotential;

/**
 * Class PlayerInitialAttributes
 *
 * @package Services\PlayerService\PlayerCreation
 */
class PlayerInitialAttributes
{
    protected $playerPotential;
    protected $playerPosition;
    protected $playerAllAttributes = [];
    protected $commonAttributes    = ['stamina', 'acceleration', 'strength'];
    protected $playerPotentialByCategory;

    public function __construct(
        array $playerPotentialByCategory,
        string $playerPosition,
        PlayerPotential $playerPotential
    ) {
        $this->playerPotential           = $playerPotential;
        $this->playerPosition            = $playerPosition;
        $this->playerPotentialByCategory = $playerPotentialByCategory;
    }

    public function getAllAttributeValues()
    {
        $this->setMainAttributes();

        return $this->playerAllAttributes;
    }

    protected function setMainAttributes()
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
     *
     * @param array  $attributes
     * @param string $attributesCategory
     */
    protected function setPrimaryAttributes(array $attributes, string $attributesCategory)
    {
        foreach ($attributes as $attribute) {
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
                // checking the object if the attribute was already set for primary or secondary value
                if (!isset($this->playerAllAttributes[$field])) {

                    $minimumAttributeValue = $this->setMinimumAttributeValue($this->playerPotentialByCategory[$category]);

                    if (isset($this->commonAttributes[$field])) {
                        $minimumAttributeValue = $minimumAttributeValue + 3;
                    }

                    $this->playerAllAttributes[$field] = (int)round(rand(9, $this->playerPotentialByCategory[$category] / 10));
                }
            }
        }
    }

    /**
     * @param int $playerPotential
     *
     * @return int
     */
    protected function setMinimumAttributeValue(int $playerPotential)
    {
        $potentialDescription = $this->playerPotential->playerPotentialLabel($playerPotential);

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
