<?php

namespace Services\PeopleService\Repositories;

use App\Models\Player\Player;
use Illuminate\Support\Facades\DB;

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

    public function getPlayersByClubId(int $clubId)
    {
        $playersWithFullDescription = [];

        $players = DB::select(
        "
                SELECT
                pp.position,
                pp.position_grade,
                p.*
                FROM players AS p
                INNER JOIN player_position AS pp ON (pp.player_id = p.id)
                WHERE p.club_id = :club_id
            ",
            ["club_id" => $clubId]
        );

        foreach ($players as $player) {
            if (!isset($playersWithFullDescription[$player->id])) {
                $playersWithFullDescription[$player->id] = $player;
                $playersWithFullDescription[$player->id]->positions = [];
            }

            $playersWithFullDescription[$player->id]->positions[$player->position] = $player->position_grade;
        }

        return $playersWithFullDescription;
    }
}
