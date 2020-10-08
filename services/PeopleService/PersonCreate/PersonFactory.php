<?php

namespace Services\PeopleService\PersonCreate;

use App\People\Staff as StaffModel;
use Services\PeopleService\PersonCreate\Types\Player;
use App\Models\Player\Player as PlayerModel;
use Services\PeopleService\PersonTypes;


class PersonFactory implements PersonFactoryInterface
{
    /**
     * @var \stdClass
     */
    private $attributes;

    /**
     * @var int
     */
    private $gameId;

    public function __construct(int $gameId)
    {
        $this->gameId = $gameId;
    }

    public function setAttributes(\stdClass $generatedAttributes)
    {
        $this->attributes = $generatedAttributes;

        return $this;
    }

    /**
     * @return PlayerModel
     */
    public function createPlayer(): PlayerModel
    {
        $player = new Player();

        return $player->create($this->attributes, $this->gameId);
    }

    public function createManager(): StaffModel
    {
        $manager = new StaffModel();
        $manager->type = 1;

        return $manager;
    }
}
