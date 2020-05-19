<?php

namespace App\Factories\Game;

use App\Models\Game\Game;
use Carbon\Carbon;

/**
 * Class GameFactory
 *
 * @package App\Factories\Game
 */
class GameFactory
{
    /**
     * @param int $userId
     *
     * @return Game|bool
     */
    public function setNewGame(int $userId)
    {
        try {
            $now  = Carbon::now()->timestamp;
            $game = new Game();

            $game->created_at   = $now;
            $game->updated_at   = $now;
            $game->game_version = null;
            $game->user_id      = $userId;
            $game->game_date    = Carbon::create((int) date("Y"), 6, 1)->toDateString();

            if ($game->save()) {
                return $game;
            }
        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }

        return false;
    }
}