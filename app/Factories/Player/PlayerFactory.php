<?php

namespace App\Factories\Player;

use App\Models\Player\Player;
use App\Models\Player\Position;
use stdClass;

class PlayerFactory
{
    /**
     * @param stdClass $playerService
     * @param int      $gameId
     *
     * @return Player
     */
    public function make(stdClass $playerService, int $gameId)
    {
        $player = new Player();

        foreach ($playerService as $field => $value) {
            if ($field == 'playerPotential' || $field == 'playerPositions') {
                continue;
            }

            $player->{$field} = $value;
        }

        $player->game_id = $gameId;
        $player->save();

        foreach ($playerService->playerPositions as $alias => $grade) {
            $position = Position::where('alias', $alias)->first();
            $player->positions()->attach($position->id, [
                'game_id'        => $gameId,
                'position_grade' => $grade,
            ]);
        }

        return $player;
    }
}
