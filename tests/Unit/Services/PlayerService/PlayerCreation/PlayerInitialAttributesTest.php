<?php

namespace Tests\Unit\Services\PlayerService\PlayerCreation;

use Services\PlayerService\PlayerCreation\PlayerInitialAttributes;
use Services\PlayerService\PlayerPotential\PlayerPotential;
use Tests\TestCase;

class PlayerInitialAttributesTest extends TestCase
{
    /**
     * @var PlayerInitialAttributes
     */
    private $playerInitialAttributesInstance;

    public function setUp(): void
    {
        parent::setUp();

        $playerPotentialByCategory = [
            "technical" => 125,
            "mental" => 126,
            "physical" => 119
        ];

        $playerPosition = "CF";

        $this->playerInitialAttributesInstance = new PlayerInitialAttributes($playerPotentialByCategory, $playerPosition, new PlayerPotential());
    }

    public function testIfPlayerAttributesAreSet()
    {
        $playerAllAttributes = $this->playerInitialAttributesInstance->getAllAttributeValues();

        // test if all attributes are here
        $this->assertEquals( 36, count($playerAllAttributes));

        // test if an attribute from each position category is here
        $this->assertGreaterThan(9, $playerAllAttributes['finishing']);
        $this->assertGreaterThan(7, $playerAllAttributes['composure']);
        $this->assertGreaterThan(5, $playerAllAttributes['marking']);
    }
}