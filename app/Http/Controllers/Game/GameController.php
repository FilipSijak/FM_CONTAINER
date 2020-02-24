<?php

namespace App\Http\Controllers\Game;

use App\Game;
use App\Http\Requests\Game\GameCreateRequest;
use App\Http\Requests\Game\GameInitRequest;
use App\Http\Resources\Club\ClubResource;
use App\Http\Resources\Game\GameResource;
use App\Models\Competition;
use App\Models\Game\BaseClubs;
use App\Models\Game\BaseCompetitions;
use App\Models\Game\BaseCountries;
use Carbon\Carbon;
use http\Env\Response;
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

        return response()->json(['data' => []]);
        // else, go and create new game
    }

    /*
     * @params - user, manager, competition, club
    */
    public function gameInit(GameInitRequest $request)
    {
        $game = new Game();

        $game->user_id = $request->get('user_id');
        $game->competition_id = $request->get('competition_id');
        $game->club_id = $request->get('club_id');
        $game->manager_id = $request->get('manager_id');

        $game->save();


        // create game and get game id
        // map base tables with game tables (countries, cities, clubs, competitions, stadiums)
        // create all players for game_id
        // create managers
    }

    /*
     * Provides options for selecting country, competition and club
    */
    public function getBaseSetup()
    {
        $clubs = BaseClubs::all();
        $clubs->forget('game_id');
        $mappedCollections = [
            'clubs' => ClubResource::collection($clubs)
        ];

        return response()->json($mappedCollections);
        // get countries
        // get competitions for country
        // get clubs for competition

        //take the selected competition, club and user and create game
    }

    public function getCountriesAndCompetitions()
    {
        $countries = BaseCountries::all();

        $countries->map(function ($country) {
            $country->competitions = $competitions = BaseCompetitions::all()->where('country_code', $country->code);
        });

        return response()->json(['data' => $countries]);
    }

    public function getClubsByCompetition(Request $request)
    {
        return ClubResource::collection(BaseClubs::all()->where('competition_id', $request->get('competition_id')));
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
