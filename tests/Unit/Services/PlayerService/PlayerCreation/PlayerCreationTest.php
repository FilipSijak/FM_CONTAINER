<?php

namespace Tests\Unit\Services\PlayerService\PlayerCreation;

use Services\PeopleService\Regens\GeneratePlayer;
use Tests\TestCase;

class PlayerCreationTest extends TestCase
{
    /**
     * @var GeneratePlayer
     */
    public $playerCreate;

    private $player;

    public function setUp(): void
    {
        parent::setUp();

        $this->playerCreate = new GeneratePlayer(150);
        $this->player = $this->playerCreate->makePlayer();
    }

    public function testIfPlayerObjectIsCreated()
    {
        $this->assertInstanceOf(\stdClass::class, $this->player);
    }

    public function testPlayerHasPersonalDetails()
    {
        $this->assertNotEmpty($this->player->first_name);
        $this->assertNotEmpty($this->player->last_name);
        $this->assertTrue((bool)strtotime($this->player->dob));
    }

    public function testPlayerHasPotentialCategories()
    {
        $this->assertNotEmpty($this->player->playerPotential);
        $this->assertGreaterThan(0, $this->player->playerPotential['technical']);
        $this->assertGreaterThan(0, $this->player->playerPotential['mental']);
        $this->assertGreaterThan(0, $this->player->playerPotential['physical']);
    }

    public function testPlayerHasAttributes()
    {
        $this->assertGreaterThan(0, $this->player->finishing);
        $this->assertGreaterThan(0, $this->player->teamwork);
        $this->assertGreaterThan(0, $this->player->pace);
    }

    public function testPlayerHasPositionsGrades()
    {
        $this->assertCount(14, $this->player->playerPositions);
    }
}