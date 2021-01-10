<?php

namespace App\GameEngine;

class SeasonEnd
{
    private $seasonId;

    public function __construct(int $seasonId)
    {
        $this->seasonId = $seasonId;
    }

    public function processSeasonEnding()
    {
        $this->updateClubsFinances();
        // trigger news updates...
    }

    private function updateClubsFinances()
    {
        //prize money...
    }
}
