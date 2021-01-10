<?php

namespace App\GameEngine;

use App\GameEngine\Interfaces\GameContainerInterface;
use App\Models\Competition\Match;
use App\Models\Competition\Season;
use App\Models\Game\Game;
use App\Models\Game\News;
use App\Repositories\CompetitionRepository;
use Carbon\Carbon;
use Services\CompetitionService\CompetitionService;
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

    /**
     * @var CompetitionService
     */
    private $competitionService;
    /**
     * @var Season
     */
    private $season;

    public function __construct(Game $game, Season $season)
    {
        $this->game = $game;
        $this->season = $season;

        $this->newsService           = new NewsService();
        //$this->transferService     = new TransferService();
        $this->matchService          = new MatchService();
        $this->competitionRepository = new CompetitionRepository();
        $this->competitionService    = new CompetitionService();
    }

    public function currentNews()
    {
        $news = $this->newsService->getNews();
        $this->storeNews($news);
    }

    public function currentDateMatches()
    {
        return $this->competitionRepository->getScheduledGamesForCompetition($this->game->game_date, 1);
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
        $currentGameDate = Carbon::parse($this->game->game_date);
        // update player training progress, morale

        // update finances

        // simulate injuries, transfers

        // every month update player value, attributes, club ranking

        // simulates only the games that are not user played and that are not already simulated while user was playing
        $this->simulateGames();

        // check if season ending
        if ($this->season->end_date == $currentGameDate->format('Y-m-d')) {
            $seasonEnd = new SeasonEnd($this->season->id);
            $seasonEnd->processSeasonEnding();
        }

        // update state (update game date)
        $this->game->game_date = $currentGameDate->addDay()->format('Y-m-d');

        $this->game->save();
    }

    public function userMatch()
    {
        $matchId = $this->competitionRepository->getUserGameIdForTheCurrentDay($this->game);

        if ($matchId) {
            $match = Match::where('id', $matchId)->firstOrFail();

            return $match;
        }

        return false;
    }

    protected function simulateGames()
    {
        $matchesByCompetition = [];
        $matches = $this->competitionRepository->getScheduledGames($this->game);

        $this->matchService->simulateRound($matches);

        foreach ($matches as $match) {
            if (!isset($matchesByCompetition[$match->competition_id])) {
                $matchesByCompetition[$match->competition_id] = [];
            }

            $matchesByCompetition[$match->competition_id][] = $match->toArray();
        }

        // create Competition Match Updater class which will take all the matches and deal with the updates
        $this->competitionService->competitionsRoundUpdate($matchesByCompetition);

        //update other stuff like club ranking, player injuries, news service etc.
    }
}