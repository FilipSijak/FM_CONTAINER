<?php

namespace Services\GameService\Interfaces;

interface GameInitialDataSeedInterface
{
    public function seedFromBaseTables(int $gameId);
}