<?php

namespace App\Factories\Game;

use App\Models\Game\Game;
use App\Http\Requests\Game\GameCreateRequest;
use Carbon\Carbon;

/**
 * Class GameFactory
 *
 * @package App\Factories\Game
 */
class GameFactory
{
    /**
     * @var GameCreateRequest
     */
    protected $request;

    /**
     * GameFactory constructor.
     *
     * @param GameCreateRequest $request
     */
    public function __construct(GameCreateRequest $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @return Game|bool
     */
    public function setNewGame()
    {
        try {
            $now  = Carbon::now()->timestamp;
            $game = new Game();

            $game->created_at   = $now;
            $game->updated_at   = $now;
            $game->game_version = null;
            $game->user_id      = $this->request->post('user_id');
            $game->club_id      = $this->request->post('club_id');

            if ($game->save()) {
                return $game;
            }
        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }

        return false;
    }
}