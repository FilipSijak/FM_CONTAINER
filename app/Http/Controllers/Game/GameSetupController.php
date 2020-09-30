<?php

namespace App\Http\Controllers\Game;

use App\GameEngine\GameCreation\CreateGame;
use App\Http\Requests\Game\GameCreateRequest;
use App\Http\Resources\Club\ClubResource;
use App\Http\Resources\Game\GameResource;
use App\Models\Game\BaseClubs;
use App\Models\Game\BaseCompetitions;
use App\Models\Game\BaseCountries;
use App\Models\Game\Game;
use App\Repositories\GameRepository;
use App\Repositories\Interfaces\GameRepositoryInterface;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GameSetupController extends Controller
{
    /**
     * @var GameRepository
     */
    protected $gameRepository;

    /**
     * GameSetupController constructor.
     *
     * @param GameRepositoryInterface $gameRepository
     */
    public function __construct(GameRepositoryInterface $gameRepository)
    {
        $this->gameRepository = $gameRepository;
    }

    /**
     * Return options for base countries, competitions, clubs when creating a new game
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json($this->gameRepository->getBaseData());
    }

    /**
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function loadGame()
    {
        $games = Game::all()->where('user_id', 1);

        if ($games->count()) {
            return GameResource::collection($games);
        }

        return response()->json(['data' => []]);
    }

    /**
     * @param GameCreateRequest $request
     */
    public function store(GameCreateRequest $request)
    {
        $this->createGameInstance = new CreateGame(
            $request->post('user_id')
        );

        $this->createGameInstance->startNewGame();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function countriesAndCompetitions()
    {
        $countries = BaseCountries::all();

        $countries->map(function ($country) {
            $country->competitions = $competitions = BaseCompetitions::all()->where('country_code', $country->code);
        });

        return response()->json(['data' => $countries]);
    }

    /**
     * @param Request $request
     *
     * @param         $competitionId
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function clubsByCompetition(Request $request, $competitionId)
    {
        return ClubResource::collection(BaseClubs::all()->where('competition_id', $competitionId));
    }
}
