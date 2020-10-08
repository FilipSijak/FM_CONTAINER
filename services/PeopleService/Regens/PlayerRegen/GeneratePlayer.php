<?php

namespace Services\PeopleService\Regens\PlayerRegen;

use Faker\Factory;
use Services\PeopleService\PlayerPosition\PlayerPosition;
use Services\PeopleService\PlayerPotential\PlayerPotential;
use stdClass;

/**
 * Class PlayerCreate
 *
 * @package Services\PlayerService\PlayerCreation
 */
class GeneratePlayer
{
    /**
     * @var stdClass
     */
    protected $player;

    /**
     * @var array
     */
    protected $playerInitialAttributes = [];

    /**
     * @var int
     */
    protected $rank;

    /**
     * @var string
     */
    protected $generatedPosition;

    /**
     * @var PlayerPosition
     */
    private $playerPosition;

    /**
     * @var PlayerPotential
     */
    private $playerPotential;

    /**
     * PlayerCreate constructor.
     *
     * @param int $rank
     */
    public function __construct(int $rank)
    {
        $this->player = new stdClass();
        $this->rank   = $rank;

        $this->playerPosition = new PlayerPosition();
        $this->playerPotential = new PlayerPotential();
    }

    /**
     * @return stdClass
     */
    public function makePlayer()
    {
        // player potential
        $this->setPlayerPotential();

        // player position
        $this->setPlayerPosition();

        // player attributes
        $this->setPlayerInitialAttributes();

        // player other positions based on attributes
        $this->setPlayerPositionList();

        // basic player information
        $this->setPlayerInfo();

        return $this->player;
    }

    /**
     *  Set potential player ability for technical, mental and physical categories
     */
    private function setPlayerPotential()
    {
        $this->player->playerPotential = (array) $this->playerPotential->calculatePlayerPotential($this->rank);
    }

    /**
     * Sets player initial main position, this is used later to assign attributes based on this position
     */
    private function setPlayerPosition()
    {
        $this->generatedPosition = $this->playerPosition->getRandomPosition();
    }

    /**
     * Sets initial attributes for a player based on his main position
     */
    private function setPlayerInitialAttributes()
    {
        $playerAttributesObject        = new PlayerInitialAttributes($this->player->playerPotential, $this->generatedPosition, $this->playerPotential);
        $this->playerInitialAttributes = $playerAttributesObject->getAllAttributeValues();

        foreach ($this->playerInitialAttributes as $key => $value) {
            $this->player->{$key} = $value;
        }
    }

    /**
     * Sets secondary positions for a player
     * Each player has a main position and potentially some other suitable positions
     */
    private function setPlayerPositionList()
    {
        foreach ( $this->playerPosition->getInitialPositionsBasedOnAttributes($this->playerInitialAttributes) as $alias => $positionGrade) {
            $this->player->playerPositions[$alias] = $positionGrade;
        }
    }

    /**
     * Sets basic player information
     */
    private function setPlayerInfo()
    {
        $faker = Factory::create();
        $dob   = $faker->dateTimeBetween($startDate = '-40 years', $endDate = '-16 years', $timezone = null);
        $dob   = date_format($dob, 'Y-m-d');

        $this->player->first_name   = $faker->firstNameMale;
        $this->player->last_name    = $faker->lastName;
        $this->player->country_code = 'GBR';
        $this->player->dob          = $dob;
    }
}
