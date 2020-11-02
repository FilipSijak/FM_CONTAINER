<?php

namespace Services\PeopleService\PlayerPosition;

use Services\PeopleService\PersonConfig\Player\PlayerPositionConfig;

/**
 * Class PlayerPosition
 *
 * @package Services\PlayerService\PlayerPosition
 */
class PlayerPosition
{
    public function getRandomPosition()
    {
        return PlayerPositionConfig::getRandomPosition();
    }

    /**
     * Sets initial positions based on provided player attributes
     * Returns a list of
     *
     * @param $attributesValues
     *
     * @return array
     */
    public function getInitialPositionsBasedOnAttributes($attributesValues): array
    {
        $positionList               = PlayerPositionConfig::PLAYER_POSITIONS;
        $positionListMainAttributes = [];

        foreach ($positionList as $position) {
            $positionListMainAttributes[$position] = array_merge(
                PlayerPositionConfig::POSITION_TECH_ATTRIBUTES[$position]['primary'],
                PlayerPositionConfig::POSITION_TECH_ATTRIBUTES[$position]['secondary'],
                PlayerPositionConfig::POSITION_MENTAL_ATTRIBUTES[$position]['primary'],
                PlayerPositionConfig::POSITION_MENTAL_ATTRIBUTES[$position]['secondary'],
                PlayerPositionConfig::POSITION_PHYSICAL_ATTRIBUTES[$position]['primary'],
                PlayerPositionConfig::POSITION_PHYSICAL_ATTRIBUTES[$position]['secondary']
            );
        }

        return $this->getAverageGradeByPosition($positionListMainAttributes, $attributesValues);
    }

    /**
     * @param array $positionsWithMainAttributes
     * @param array $playerAttributeValues
     *
     * @return array
     */
    private function getAverageGradeByPosition(array $positionsWithMainAttributes, array $playerAttributeValues): array
    {
        $averageGradeForPosition = [];

        foreach ($positionsWithMainAttributes as $position => $positionAttributes) {
            $averageGradeForPosition[$position] = 0;
            $count                              = 0;

            foreach ($positionAttributes as $attribute) {
                $count++;
                $averageGradeForPosition[$position] += $playerAttributeValues[$attribute];
            }

            $averageGradeForPosition[$position] = $averageGradeForPosition[$position] / $count;
        }

        asort($averageGradeForPosition);

        return array_reverse($averageGradeForPosition);
    }
}

