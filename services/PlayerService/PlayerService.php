<?php

namespace Services\PlayerService;

use Services\PlayerService\Interfaces\PlayerServiceInterface;
use Services\PlayerService\PlayerCreation\PlayerCreate;

class PlayerService implements PlayerServiceInterface
{
    /**
     * @var int
     */
    protected $playerPotential;

    public function setPlayerPotential(int $playerPotential) {
        $this->playerPotential = $playerPotential;

        return $this;
    }

    public function createPlayer()
    {
        $playerCreate = new PlayerCreate($this->playerPotential);

        return $playerCreate->makePlayer($this->playerPotential);
    }
}
