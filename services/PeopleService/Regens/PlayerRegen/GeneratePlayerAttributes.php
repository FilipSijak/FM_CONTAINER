<?php

namespace Services\PeopleService\Regens\PlayerRegen;

use Services\PeopleService\PersonConfig\Player\PlayerFields;
use Services\PeopleService\PersonPotential\PersonPotential;
use Services\PeopleService\PlayerPosition\PlayerPosition;
use Services\PeopleService\Regens\PersonAttributesGenerator;
use stdClass;

/**
 * Class PlayerCreate
 *
 * @package Services\PlayerService\PlayerCreation
 */
class GeneratePlayerAttributes extends PersonAttributesGenerator
{
    /**
     * @var string
     */
    protected $generatedPosition;

    /**
     * @var PlayerPosition
     */
    private $playerPosition;

    private $playerInitialAttributes;

    /**
     * @var PersonPotential
     */
    private $personPotential;


    /**
     * @return stdClass
     */
    public function generateAttributes()
    {
        $this->playerPosition  = new PlayerPosition();
        $this->personPotential = new PersonPotential();

        $this->setPlayerPosition();

        $this->setCategoriesPotential();

        $this->setInitialAttributes();

        $this->setPlayerPositionList();

        $this->setPersonInfo();

        return $this->person;
    }

    /**
     * Sets player initial main position
     */
    protected function setPlayerPosition()
    {
        $this->person->position = $this->personConfig->position;
    }

    /**
     *  Set potential player ability for technical, mental and physical categories
     */
    protected function setCategoriesPotential()
    {
        $this->personPotential->setPersonCategories(PlayerFields::PERSON_ATTRIBUTE_CATEGORIES);
        $this->person->potentialByCategory = (array)$this->personPotential->calculatePersonPotential($this->personConfig->potential);
        $this->person->potential           = $this->personConfig->potential;
    }

    /**
     * Sets initial attributes for a player based on his main position
     */
    protected function setInitialAttributes()
    {
        $initialAttributes = new InitialAttributes(
            $this->person->potentialByCategory,
            $this->personConfig->position
        );

        $this->playerInitialAttributes = $initialAttributes->getAllAttributeValues();

        foreach ($this->playerInitialAttributes as $attribute => $value) {
            $this->person->{$attribute} = $value;
        }
    }

    /**
     * Sets secondary positions for a player
     * Each player has a main position and potentially some other suitable positions
     */
    private function setPlayerPositionList()
    {
        $initialAttributes = $this->playerPosition->getInitialPositionsBasedOnAttributes($this->playerInitialAttributes);

        foreach ($initialAttributes as $alias => $positionGrade) {
            $this->person->playerPositions[$alias] = $positionGrade;
        }
    }
}
