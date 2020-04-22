<?php

namespace Services\PlayerService;

use Services\PlayerService\Interfaces\PlayerServiceInterface;
use Services\PlayerService\PlayerCreation\PlayerCreate;

class PlayerService implements PlayerServiceInterface
{
    protected $playerCreate;

    public function __construct()
    {

    }

    public function createPlayer()
    {
        $this->playerCreate = new PlayerCreate(184);

        return $this->playerCreate->makePlayer();
    }
}
