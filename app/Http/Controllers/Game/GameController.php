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

        if ($games->count()) {
            return GameResource::collection($games);
        }

        // else, go and create new game
    }

    public function gameInit()
    {
        // map base clubs to clubs with game_id
        // create all players for game_id
    }

    /*
     * Provides options for selecting country, competition and club
    */
    public function getBaseSetup()
    {
        // get countries
        // get competitions for country
        // get clubs for competition

        //take the selected competition, club and user and create game
    }

    public function store(GameCreateRequest $request)
    {
        $now = Carbon::now()->timestamp;
        $game = new Game();

        $game->created_at = $now;
        $game->updated_at = $now;
        $game->game_version = null;
        $game->user_id = $request->post('user_id');
        $game->club_id = $request->post('club_id');
        $game->competition_id = $request->post('competition_id');

        //$game->save();
        return new GameResource($game);
    }
}
