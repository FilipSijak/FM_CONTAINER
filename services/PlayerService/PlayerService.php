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

    public function createPlayer(int $rank)
    {
        $this->playerCreate = new PlayerCreate($rank);

        return $this->playerCreate->makePlayer();
    }
}
