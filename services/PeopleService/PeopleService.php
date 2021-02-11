<?php

namespace Services\PeopleService;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Services\PeopleService\Interfaces\PeopleServiceInterface;
use Services\PeopleService\PersonCreate\PersonFactory;
use Services\PeopleService\PlayerPosition\PlayerPosition;
use Services\PeopleService\Regens\PlayerRegen\GeneratePlayerAttributes;
use Services\PeopleService\Regens\StaffRegen\GenerateManagerAttributes;

class PeopleService implements PeopleServiceInterface
{
    /**
     * @var int
     */
    private $gameId;

    /**
     * @var int
     */

    private $personType;
    /**
     * @var int
     */
    private $personPotential;

    /**
     * @param int $personPotential
     * @param int $gameId
     * @param int $personType
     *
     * @return $this
     */
    public function setPersonConfiguration(int $personPotential, int $gameId, int $personType): PeopleService
    {
        $this->personPotential = $personPotential;
        $this->gameId          = $gameId;
        $this->personType      = $personType;

        return $this;
    }

    /**
     * @return EloquentModel
     */
    public function createPerson(): ?EloquentModel
    {
        $personFactory = new PersonFactory($this->gameId);
        $person        = null;

        switch ($this->personType) {
            case PersonTypes::PLAYER:
                $player              = new GeneratePlayerAttributes($this->personPotential, PersonTypes::PLAYER);
                $generatedAttributes = $player->generateAttributes();
                $person              = $personFactory->setAttributes($generatedAttributes)->createPlayer();

                break;
            case PersonTypes::MANAGER:
                $manager             = new GenerateManagerAttributes($this->personPotential, PersonTypes::MANAGER);
                $generatedAttributes = $manager->generateAttributes();
                $person              = $personFactory->setAttributes($generatedAttributes)->createManager();

                break;
        }

        return $person;
    }

    /**
     * @param array $playerAttributes
     *
     * @return array
     */
    public function generatePlayerPositionList(array $playerAttributes): array
    {
        $playerPosition = new PlayerPosition();

        return $playerPosition->getInitialPositionsBasedOnAttributes($playerAttributes);
    }
}
