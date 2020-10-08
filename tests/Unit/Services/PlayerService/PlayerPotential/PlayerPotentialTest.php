<?php

namespace Tests\Unit\Services\PlayerService\PlayerPotential;

use Services\PeopleService\PlayerPotential\PlayerPotential;
use Tests\TestCase;

class PlayerPotentialTest extends TestCase
{
    /**
     * @var PlayerPotential
     */
    public $playerPotential;

    public function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $this->playerPotential = new PlayerPotential();
    }

    public function test_player_potential_label()
    {
        $playerPotentialCoefficient = 110;

        $potentialLabel = $this->playerPotential->playerPotentialLabel($playerPotentialCoefficient);

        $this->assertEquals('normal', $potentialLabel);
    }

    public function test_player_potential_object_attributes()
    {
        $playerPotentialCoefficient = 110;

        $playerPotential = $this->playerPotential->calculatePlayerPotential($playerPotentialCoefficient);

        $this->assertObjectHasAttribute('technical', $playerPotential);
        $this->assertObjectHasAttribute('mental', $playerPotential);
        $this->assertObjectHasAttribute('physical', $playerPotential);
    }
}