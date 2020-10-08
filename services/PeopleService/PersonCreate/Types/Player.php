<?php

namespace Services\PeopleService\PersonCreate\Types;

use App\Models\Player\Player as PlayerModel;
use App\Models\Player\Position;
use stdClass;

class Player
{
    /**
     * @param stdClass $playerService
     * @param int      $gameId
     *
     * @return PlayerModel
     */
    public function create(stdClass $playerService, int $gameId): PlayerModel
    {
        $player = new PlayerModel();

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
