<?php

namespace App\GameEngine;

use App\GameEngine\Interfaces\GameContainerInterface;
use App\Models\Game\Game;
use Illuminate\Support\Carbon;
use Services\NewsService\Interfaces\NewsServiceInterface;
use Services\NewsService\NewsService;

class GameContainer implements GameContainerInterface
{
    /**
     * @var NewsService
     */
    protected $newsService;

    public function __construct(
    ) {
        $this->newsService = new NewsService();
    }

    public function currentDay(Game $game)
    {
        // check news (games, transfers, league table, achievements)
        $this->newsService->getNews();

        // check transfers (transfer rumors, transfer proposals, transfer deals, contracts etc.)

        // check matches (any matches today? cant move forward if matches for the day are not played)
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