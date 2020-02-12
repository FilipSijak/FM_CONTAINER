<?php

namespace App\Http\Controllers\Game;

use App\Game;
use App\Http\Requests\Game\GameCreateRequest;
use App\Http\Resources\Game\GameResource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GameController extends Controller
{
    public function index()
    {
        $games = Game::all()->where('user_id', 1);
        dd($games);
    }

    public function store(GameCreateRequest $request)
    {
        $now = Carbon::now()->timestamp;
        $game = new Game();

        $game->created_at = $now;
        $game->updated_at = $now;
        $game->game_version = null;
        $game->user_id = $request->post('user_id');

        $game->save();

        return new GameResource($game);
    }
}
