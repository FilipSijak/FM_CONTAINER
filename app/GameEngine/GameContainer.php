<?php

namespace App\GameEngine;

use App\GameEngine\Interfaces\GameContainerInterface;
use App\Models\Game\Game;
use App\Models\Game\News;
use App\Repositories\CompetitionRepository;
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
    /**
     * @var CompetitionRepository
     */
    private $competitionRepository;

    /**
     * @var Game
     */
    private $game;

    public function __construct()
    {
        $this->newsService           = new NewsService();
        //$this->transferService       = new TransferService();
        $this->matchService          = new MatchService();
        $this->competitionRepository = new CompetitionRepository();
    }

    public function setGame(Game $game)
    {
        $this->game = $game;

        return $this;
    }

    public function currentNews()
    {
        // check news
        $news = $this->newsService->getNews();
        $this->storeNews($news);


        // check transfers (transfer rumors, transfer proposals, transfer deals, contracts etc.)
        //$this->transferService->processTransferBids();

        // check matches (any matches today? cant move forward if matches for the day are not played)
        $matches = $this->competitionRepository->getScheduledGamesForCompetition($this->game, 1);


        //return all the resources
    }

    private function storeNews($news)
    {
        foreach ($news as $item) {
            $newsModel          = new News();
            $newsModel->title   = $item->title;
            $newsModel->content = $item->content;
            $newsModel->game_id = $this->game->id;

            $newsModel->save();
        }
    }

    public function moveForward()
    {
        // update player training progress, morale

        // update finances

        // simulate injuries, transfers

        // every month update player value, attributes, club ranking

        // update state (update game date)
        $this->game->game_date = Carbon::parse($this->game->game_date)->addDay();
    }

    //POST
    public function simulateGames()
    {
        $matches = $this->competitionRepository->getScheduledGames($this->game);

        $this->matchService->simulateRound($matches);
    }
}