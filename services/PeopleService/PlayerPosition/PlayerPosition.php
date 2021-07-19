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
    public function getRandomPosition(): string
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
            if (!isset($positionListMainAttributes[$position]['primary'])) {
                $positionListMainAttributes[$position]['primary'] = [];
            }

            if (!isset($positionListMainAttributes[$position]['secondary'])) {
                $positionListMainAttributes[$position]['secondary'] = [];
            }

            $positionListMainAttributes[$position]['primary'][] = array_merge(
                PlayerPositionConfig::POSITION_TECH_ATTRIBUTES[$position]['primary'],
                PlayerPositionConfig::POSITION_MENTAL_ATTRIBUTES[$position]['primary'],
                PlayerPositionConfig::POSITION_PHYSICAL_ATTRIBUTES[$position]['primary'],
            );

            $positionListMainAttributes[$position]['secondary'][] = array_merge(
                PlayerPositionConfig::POSITION_TECH_ATTRIBUTES[$position]['secondary'],
                PlayerPositionConfig::POSITION_MENTAL_ATTRIBUTES[$position]['secondary'],
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
        $positionsLimit          = 3;

        foreach ($positionsWithMainAttributes as $position => $positionAttributes) {
            if (!$positionsLimit) {
                break;
            }

            $averageGradeForPosition[$position] = 0;
            $count                              = 0;

            if (isset($positionAttributes['primary'][0])) {
                foreach ($positionAttributes['primary'][0] as $attribute) {
                    $count++;

                    if (!isset($playerAttributeValues[$attribute])) {
                        echo $attribute;
                    } else {
                        $averageGradeForPosition[$position] += $playerAttributeValues[$attribute] + 3;
                    }

                }
            }

            $averageGradeForPosition[$position] = $averageGradeForPosition[$position] / $count;
            $positionsLimit--;
        }

        asort($averageGradeForPosition);

        return array_reverse($averageGradeForPosition);
    }
}
