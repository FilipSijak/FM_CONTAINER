<?php

namespace App\Http\Controllers\Game;

use App\GameEngine\GameContainer;
use App\GameEngine\Interfaces\GameContainerInterface;
use App\Http\Controllers\CoreController;
use App\Models\Game\Game;
use App\Repositories\Interfaces\NewsRepositoryInterface;
use App\Repositories\Interfaces\SeasonRepositoryInterface;
use Illuminate\Http\Request;
use Services\GameService\Interfaces\GameInitialDataSeedInterface;
use Services\PeopleService\PeopleService;

class GameController extends CoreController
{
    /**
     * @var GameInitialDataSeedInterface
     */
    protected $gameInitialDataSeed;

    /**
     * @var PeopleService
     */
    protected $playerService;

    /**
     * @var SeasonRepositoryInterface
     */
    protected $seasonRepository;

    /**
     * @var GameContainerInterface
     */
    protected $gameContainer;
    /**
     * @var NewsRepositoryInterface
     */
    private $newsRepository;
    private $season;

    /**
     * GameController constructor.
     *
     * @param Request                   $request
     * @param SeasonRepositoryInterface $seasonRepository
     * @param NewsRepositoryInterface   $newsRepository
     */
    public function __construct(
        Request $request,
        SeasonRepositoryInterface $seasonRepository,
        NewsRepositoryInterface $newsRepository
    ) {
        parent::__construct($request);

        $this->seasonRepository = $seasonRepository;
        $this->newsRepository   = $newsRepository;
        $this->season           = $seasonRepository->getCurrentSeasonByGameId($this->game->id);
        $this->gameContainer    = new GameContainer($this->game, $this->season);
    }

    /**
     * Get current news for the day
     */
    public function news()
    {
        //get news
        $this->newsRepository->getCurrentNews();

        //return NewsResource::collection(News::where('game_id', 1)->get());
    }

    public function currentDay()
    {
        // @TODO read current news

        // @TODO check if user has a match
    }

    public function matchDay()
    {
        $match = $this->gameContainer->userMatch();
    }

    /**
     * Update game state
     *
     * @param Game $game
     */
    public function nextDay()
    {
        // @TODO check if all the games were simulated to proceed

        $this->gameContainer->moveForward();
    }
}
