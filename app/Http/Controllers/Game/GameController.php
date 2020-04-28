<?php

namespace App\Http\Controllers\Game;

use App\Factories\Game\GameFactory;
use App\Http\Controllers\Controller;
use App\Http\Requests\Game\GameCreateRequest;
use App\Http\Resources\Club\ClubResource;
use App\Http\Resources\Game\GameResource;
use App\Models\Club\Club;
use App\Models\Game\BaseClubs;
use App\Models\Game\BaseCompetitions;
use App\Models\Game\BaseCountries;
use App\Models\Game\Game;
use App\Models\Player\Player;
use App\Models\Player\Position;
use App\Repositories\Interfaces\GameRepositoryInterface;
use Illuminate\Http\Request;
use Services\ClubService\GeneratePeople\InitialClubPeoplePotential;
use Services\GameService\Interfaces\GameInitialDataSeedInterface;
use Services\PlayerService\PlayerService;
use stdClass;

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

    protected $gameId;

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
        PlayerService $playerService
    ) {
        $this->gameRepository      = $gameRepository;
        $this->gameInitialDataSeed = $gameInitialDataSeed;
        $this->playerService       = $playerService;
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
     */
    public function store(GameCreateRequest $request)
    {
        $gameFactory = new GameFactory($request);

        $game = $gameFactory->setNewGame();

        $this->gameId = $game->id;

        if ($game instanceof Game) {
            $this->gameInit($game);
        }
    }

    /**
     * @param Game $game
     */
    public function gameInit(Game $game)
    {
        // map base tables with game tables (countries, cities, clubs, competitions, stadiums)
        // seed only if game tables are empty
        //$dataSeed = $this->gameInitialDataSeed->seedFromBaseTables($game);

        // create all players for game_id
        //create a single player

        $clubs                 = Club::all();
        $initialPlayerCreation = new InitialClubPeoplePotential();

        foreach ($clubs as $club) {
            $playerPotentialList = $initialPlayerCreation->getPlayerPotentialListByClubRank($club->rank);

            foreach ($playerPotentialList as $playerPotential) {
                $servicePlayer = $this->playerService->createPlayer($playerPotential);
                $player = $this->createPlayer($servicePlayer);
                $player->clubs()->attach($club->id);
            }
        }
    }

    private function createPlayer(stdClass $servicePlayer)
    {
        $player = new Player();

        foreach ($servicePlayer as $field => $value) {
            if ($field == 'playerPotential' || $field == 'playerPositions') {
                continue;
            }

            $player->{$field} = $value;
        }

        $player->game_id = $this->gameId;
        $player->save();

        foreach ($servicePlayer->playerPositions as $alias => $grade) {
            $position = Position::where('alias', $alias)->first();
            $player->positions()->attach($position->id, [
                'game_id'        => $this->gameId,
                'position_grade' => $grade,
            ]);
        }

        return $player;
    }
}
