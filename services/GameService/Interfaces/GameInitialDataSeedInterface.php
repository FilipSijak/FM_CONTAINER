<?php

namespace Services\GameService\Interfaces;

use App\Models\Game\Game;

interface GameInitialDataSeedInterface
{
    public function seedFromBaseTables(Game $game);
}