<?php

namespace Services\PeopleService;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Services\PeopleService\Interfaces\PeopleServiceInterface;
use Services\PeopleService\PersonCreate\PersonFactory;
use Services\PeopleService\Regens\PlayerRegen\GeneratePlayer;
use Services\PeopleService\Regens\StaffRegen\GenerateManager;

class PeopleService implements PeopleServiceInterface
{
    /**
     * @var int
     */
    private   $gameId;

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
    public function setPersonConfiguration(int $personPotential, int $gameId, int $personType)
    {
        $this->personPotential = $personPotential;
        $this->gameId = $gameId;
        $this->personType = $personType;

        return $this;
    }

    /**
     * @return EloquentModel
     */
    public function createPerson(): EloquentModel
    {
        $personFactory = new PersonFactory($this->gameId);
        $person = null;

        switch ($this->personType) {
            case PersonTypes::PLAYER:
                $player = new GeneratePlayer($this->personPotential);
                $person = $personFactory->setAttributes($player->makePlayer())->createPlayer();

                break;
            case PersonTypes::MANAGER:
                $manager = new GenerateManager($this->personPotential);
                $person  = $personFactory->setAttributes($manager->makeManager())->createManager();

                break;
        }

        return $person;
    }
}
