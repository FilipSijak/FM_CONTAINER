<?php

namespace App\Http\Controllers\Game;

use App\Factories\Game\GameFactory;
use App\Models\Game\Game;
use App\Http\Requests\Game\GameCreateRequest;
use App\Http\Resources\Club\ClubResource;
use App\Http\Resources\Game\GameResource;
use App\Models\Game\BaseClubs;
use App\Models\Game\BaseCompetitions;
use App\Models\Game\BaseCountries;
use App\Repositories\Interfaces\GameRepositoryInterface;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Services\GameService\Interfaces\GameInitialDataSeedInterface;

class GameController extends Controller
{
    /**
     * @var GameRepositoryInterface
     */
    protected $gameRepository;

    /**
     * @var GameInitialDataSeedInterface
     */
    protected $gameInitialDataSeed;

    /**
     * GameController constructor.
     *
     * @param GameRepositoryInterface $gameRepository
     */
    public function __construct(
        GameRepositoryInterface $gameRepository,
        GameInitialDataSeedInterface $gameInitialDataSeed
    )
    {
        $this->gameRepository = $gameRepository;
        $this->gameInitialDataSeed = $gameInitialDataSeed;
    }

    /**
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCountriesAndCompetitions()
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
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getClubsByCompetition(Request $request)
    {
        return ClubResource::collection(BaseClubs::all()->where('competition_id', $request->get('competition_id')));
    }

    /**
     * @param GameCreateRequest $request
     *
     * @return GameResource
     */
    public function store(GameCreateRequest $request)
    {
        $gameFactory = new GameFactory($request);

        $game = $gameFactory->setNewGame();

        if ($game instanceof Game) {
            $this->gameInit($game);
        }
    }

    public function gameInit(Game $game)
    {
        //$dataSeed = $this->gameInitialDataSeed->seedFromBaseTables($game);
        // map base tables with game tables (countries, cities, clubs, competitions, stadiums)

        // create all players for game_id

        // create managers
    }
}
