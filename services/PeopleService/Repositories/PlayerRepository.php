<?php

namespace Services\PeopleService\Repositories;

use App\Models\Player\Player;

class PlayerRepository
{
    public function getPlayerById(int $playerId)
    {
        return Player::where('id',$playerId);
    }

    public function getPlayerClub(int $playerId)
    {
        $player = Player::findOrFail($playerId);

        return $player->club();
    }
}
