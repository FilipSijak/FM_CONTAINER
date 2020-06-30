<?php

namespace Tests\Unit\Services\PlayerService\Config;

use Services\PlayerService\PlayerConfig\PlayerPositionConfig;
use Tests\TestCase;

class PlayerAttributesTest extends TestCase
{
    /**
     * @var PlayerPositionConfig
     */
    public $playerPositionConfig;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->playerPositionConfig = new PlayerPositionConfig();
    }

    public function test_player_should_receive_random_position()
    {
        $randomPosition = $this->playerPositionConfig::getRandomPosition();

        $this->assertContains($randomPosition, $this->playerPositionConfig::PLAYER_POSITIONS);
    }

    public function test_random_position_will_return_main_attributes()
    {
        $mainAttributes = $this->playerPositionConfig::getPositionMainAttributes('CB');
        $technical = $mainAttributes['technical']['primary'];

        $this->assertContains('heading', $technical);
    }
}