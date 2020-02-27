<?php

namespace Services\PlayerService;

use Services\PlayerService\PlayerCreation\PlayerCreate;

class PlayerService
{
    protected $playerCreate;

    public function __construct()
    {
        $this->playerCreate = new PlayerCreate();
    }

    public function createPlayer()
    {
        $this->playerCreate->setupPlayer(184);
    }
}
