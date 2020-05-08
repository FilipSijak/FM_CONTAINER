<?php

namespace App\Http\Controllers\Game;

use App\Factories\Competition\SeasonFactory;
use App\GameEngine\GameCreation\CreateGame;
use App\Http\Controllers\Controller;
use App\Http\Requests\Game\GameCreateRequest;
use App\Http\Resources\Club\ClubResource;
use App\Http\Resources\Game\GameResource;
use App\Models\Game\BaseClubs;
use App\Models\Game\BaseCompetitions;
use App\Models\Game\BaseCountries;
use App\Models\Game\Game;
use App\Repositories\Interfaces\GameRepositoryInterface;
use App\Repositories\Interfaces\SeasonRepositoryInterface;
use Illuminate\Http\Request;
use Services\GameService\Interfaces\GameInitialDataSeedInterface;
use Services\PlayerService\PlayerService;

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
     * @var PlayerService
     */
    protected $playerService;

    /**
     * @var SeasonRepositoryInterface
     */
    protected $seasonRepository;

    protected $createGameInstance;

    /**
     * GameController constructor.
     *
     * @param GameRepositoryInterface      $gameRepository
     * @param GameInitialDataSeedInterface $gameInitialDataSeed
     * @param PlayerService                $playerService
     */
    public function __construct(
        GameRepositoryInterface $gameRepository,
        GameInitialDataSeedInterface $gameInitialDataSeed,
        PlayerService $playerService,
        SeasonRepositoryInterface $seasonRepository
    ) {
        $this->gameRepository      = $gameRepository;
        $this->gameInitialDataSeed = $gameInitialDataSeed;
        $this->playerService       = $playerService;
        $this->seasonRepository = $seasonRepository;
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
     * @param GameCreateRequest $request
     */
    public function store(GameCreateRequest $request)
    {
        $this->createGameInstance = new CreateGame(
            $request->post('user_id')
        );

        $seasonFactory = new SeasonFactory();

        $this->createGameInstance->startNewGame()
                                 ->populateFromBaseTables($this->gameInitialDataSeed)
                                 ->setAllClubs()
                                 ->assignPlayersToClubs($this->playerService)
                                 ->assignBalancesToClubs()
                                 ->assignSeasonToGame($seasonFactory)
                                 ->assignCompetitionsToSeason();
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
}
