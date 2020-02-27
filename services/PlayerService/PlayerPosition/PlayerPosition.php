<?php

namespace Services\PlayerService\PlayerPosition;

use Services\PlayerService\PlayerConfig\PlayerPosition as PositionConfig;

class PlayerPosition
{
    public static function setRandomPosition()
    {
        return PositionConfig::getRandomPosition();
    }

    /*
     * Sets initial positions based on player attributes
     @param array - list of player attributes and values to decide suitable positions
    */
    public static function setInitialPositionsBasedOnAttributes($attributesValues): array
    {
        $positionsMainAttributes = self::getMainAttributesForPosition();
        return self::getAverageGradeByPosition($positionsMainAttributes, $attributesValues);
    }

    private static function getMainAttributesForPosition(): array
    {
        $positionList = PositionConfig::PLAYER_POSITIONS;
        $positionListMainAttributes = [];

        foreach ($positionList as $position) {
            $positionListMainAttributes[$position] = array_merge(
                PositionConfig::POSITION_TECH_ATTRIBUTES[$position]['primary'],
                PositionConfig::POSITION_TECH_ATTRIBUTES[$position]['secondary'],
                PositionConfig::POSITION_MENTAL_ATTRIBUTES[$position]['primary'],
                PositionConfig::POSITION_MENTAL_ATTRIBUTES[$position]['secondary'],
                PositionConfig::POSITION_PHYSICAL_ATTRIBUTES[$position]['primary'],
                PositionConfig::POSITION_PHYSICAL_ATTRIBUTES[$position]['secondary']
            );
        }

        return $positionListMainAttributes;
    }

    /*
     * Sum value for every position
    */
    public static function getAverageGradeByPosition(array $positionsWithMainAttributes, array $playerAttributeValues): array
    {
        $averageGradeForPosition = [];

        foreach ($positionsWithMainAttributes as $position => $positionAttributes) {
            $averageGradeForPosition[$position] = 0;
            $count = 0;

            foreach ($positionAttributes as $attribute) {
                $count++;
                $averageGradeForPosition[$position] += $playerAttributeValues[$attribute];
            }

            $averageGradeForPosition[$position] = $averageGradeForPosition[$position] / $count;
        }

        asort($averageGradeForPosition);
        return array_reverse($averageGradeForPosition);
    }

    /*
     * Get list of player positions
    */
    public static function getPositionListFromDB($player)
    {

    }

    /*
     * Sets initial positions based on player attributes
     * New position should be set only after player is trained for it
    */
    public static function setPosition($player, $position)
    {

    }

    /*
     * Remove player position from the list
    */
    public static function removePosition($player, $position)
    {

    }
}

