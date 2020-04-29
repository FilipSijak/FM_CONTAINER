<?php

namespace App\Factories\Club;

use App\Models\Club\Balance;
use App\Models\Club\Club;

class BalanceFactory
{
    const BASIC_BALANCE_MULTIPLIER = 5000000;

    const STARTING_DEBT = 0;

    public function make(Club $club, int $gameId)
    {
        $balance = new Balance();
        $balance->game_id = $gameId;
        $balance->balance = $club->rank * self::BASIC_BALANCE_MULTIPLIER;
        $balance->debt = self::STARTING_DEBT;

        $club->balance()->save($balance);
    }
}
