<?php

namespace App\Repositories\Player;

use App\Models\Club\Club;
use Illuminate\Database\Eloquent\Collection;

class PlayerRepository
{
    public function playersByClub(int $clubId): Collection
    {
        $club = Club::where('id', $clubId)->firstOrFail();

        return $club->players()->get();
    }
}