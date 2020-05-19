<?php

namespace App\GameEngine;

use App\GameEngine\Interfaces\GameContainerInterface;
use App\Models\Game\Game;
use Carbon\Carbon;
use Services\MatchService\MatchService;
use Services\NewsService\NewsService;
use Services\TransferService\TransferService;

class GameContainer implements GameContainerInterface
{
    /**
     * @var NewsService
     */
    protected $newsService;

    /**
     * @var TransferService
     */
    private $transferService;
    /**
     * @var MatchService
     */
    private $matchService;

    public function __construct(
    ) {
        $this->newsService = new NewsService();
        $this->transferService = new TransferService();
        $this->matchService = new MatchService();
    }

    public function currentDay(Game $game)
    {
        // check news (games, transfers, league table, achievements)
        $this->newsService->getNews();

        // check transfers (transfer rumors, transfer proposals, transfer deals, contracts etc.)
        $this->transferService->processTransferBids();

        // check matches (any matches today? cant move forward if matches for the day are not played)
        $this->matchService->simulateRound();
    }

    public function moveForward(Game $game)
    {
        // update state (update game date)
        $game->game_date = Carbon::parse($game->game_date)->addDay();
        dd($game);
        // injuries

        // every month update player value, attributes, morale
    }
}