<?php

namespace App\Http\Controllers\Game;

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

    /**
     * GameController constructor.
     *
     * @param Request                   $request
     * @param $
     * @param SeasonRepositoryInterface $seasonRepository
     * @param GameContainerInterface    $gameContainer
     * @param NewsRepositoryInterface   $newsRepository
     */
    public function __construct(
        Request $request,
        SeasonRepositoryInterface $seasonRepository,
        GameContainerInterface $gameContainer,
        NewsRepositoryInterface $newsRepository
    ) {
        parent::__construct($request);

        $this->seasonRepository    = $seasonRepository;
        $this->gameContainer       = $gameContainer;
        $this->newsRepository      = $newsRepository;
    }

    /**
     * Get current news for the day
     *
     * @param Game $game
     */
    public function news()
    {
        $this->gameContainer->setGame($this->game)->currentNews();

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
        $match = $this->gameContainer->setGame($this->game)->userMatch();
    }

    /**
     * Update game state
     *
     * @param Game $game
     */
    public function nextDay()
    {
        // @TODO check if all the games were simulated to proceed

        $this->gameContainer->setGame($this->game)->moveForward();
    }
}
