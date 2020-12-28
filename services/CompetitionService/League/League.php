<?php

namespace Services\CompetitionService\League;

use App\Factories\Competition\MatchFactory;
use App\Factories\Competition\PointsFactory;
use App\Models\Competition\Competition;
use Services\CompetitionService\CompetitionsConfig\TournamentConfig;

class League
{
    protected $clubs;

    protected $games = [];

    protected $fixed;

    protected $home = true;

    protected $numberOfGamesInRound;

    /**
     * League constructor.
     *
     * @param array $clubs - array of clubs id's
     * @param int   $competitionId
     * @param int   $seasonId
     */
    public function __construct(array $clubs, int $competitionId, int $seasonId)
    {
        $this->clubs                = $clubs;
        $this->competitionId        = $competitionId;
        $this->seasonId             = $seasonId;
        $this->competitionSize      = count($this->clubs);
        $this->numberOfGamesInRound = $this->competitionSize / 2;
        $this->fixed                = !empty($this->clubs) ? $this->clubs[0] : 0;
    }

    public function setLeagueCompetition()
    {
        $pointsFactory    = new PointsFactory();
        $tournamentConfig = new TournamentConfig();
        $leagueFixtures   = $this->generateLeagueGames();
        $carbonCopy       = $tournamentConfig->getStartDate()->copy();
        $seasonStart      = $carbonCopy->modify("next Sunday");

        $this->populateLeagueFixtures($leagueFixtures, $this->competitionId, $seasonStart);

        $competition = Competition::find($this->competitionId);

        foreach ($this->clubs as $clubId) {
            $competition->seasons()->attach(
                $this->seasonId,
                [
                    'club_id' => $clubId,
                ]
            );

            $pointsFactory->make(
                $clubId,
                $this->competitionId,
                $this->seasonId
            );
        }
    }

    /**
     * The tournament scheduling algorithm with the idea taken from the Berger tables in planning of tournaments
     *
     * Berger tables src: https://en.wikipedia.org/wiki/Round-robin_tournament#Scheduling_algorithm
     *
     * @return array
     */
    public function generateLeagueGames()
    {
        // Creates first round
        for ($i = 0, $k = $this->competitionSize - 1; $i < $k; $i++, $k--) {
            $game             = new \stdClass();
            $game->homeTeamId = $this->clubs[$i];
            $game->awayTeamId = $this->clubs[$k];

            $this->games[] = $game;
        }

        // Based on first round games, this will loop and set the rest of rounds
        for ($i = 0; $i < $this->competitionSize - 2; $i++) {
            $this->generateSingleRoundGames();
        }

        $this->swapTeamsForRematch();

        return $this->games;
    }

    /**
     * Generates all the games for a single round
     *
     * @return array
     */
    private function generateSingleRoundGames()
    {
        $localGames = [];
        $this->home = !$this->home;
        $gamesCount = count($this->games);

        for ($i = $gamesCount - $this->numberOfGamesInRound, $k = $gamesCount - 1; $i < $gamesCount; $i++, $k--) {
            $game = new \stdClass();

            // first game in the round
            if ($i == $gamesCount - $this->numberOfGamesInRound) {
                if ($this->home) {
                    $game->homeTeamId = $this->fixed;
                    $game->awayTeamId = $this->games[$k]->awayTeamId;
                } else {
                    $game->homeTeamId = $this->games[$k]->awayTeamId;
                    $game->awayTeamId = $this->fixed;
                }

                $localGames[] = $game;
            } else {
                // when k is at the first 0 index of previous round
                if ($k == $gamesCount - $this->numberOfGamesInRound) {
                    // decides last game home/away team depending on where the fixed was (home or away)
                    if ($this->home) {
                        $game->homeTeamId = $this->games[$k]->homeTeamId;
                        $game->awayTeamId = $this->games[$k + 1]->homeTeamId;

                        $localGames[] = $game;
                    } else {
                        $game->homeTeamId = $this->games[$k]->awayTeamId;
                        $game->awayTeamId = $this->games[$k + 1]->homeTeamId;

                        $localGames[] = $game;
                    }

                    continue;
                }

                if ($k > 0) {
                    $game->homeTeamId = $this->games[$k]->awayTeamId;
                    $game->awayTeamId = $this->games[$k + 1]->homeTeamId;

                    $localGames[] = $game;
                }
            }
        }

        return $this->games = array_merge($this->games, $localGames);
    }

    /**
     * @return array
     */
    private function swapTeamsForRematch()
    {
        $rematchGames = [];

        foreach ($this->games as $game) {
            $rematch             = new \stdClass();
            $rematch->homeTeamId = $game->awayTeamId;
            $rematch->awayTeamId = $game->homeTeamId;

            $rematchGames[] = $rematch;
        }

        return $this->games = array_merge($this->games, $rematchGames);
    }

    /**
     * @param array $leagueFixtures
     * @param       $competitionId
     */
    public function populateLeagueFixtures(array $leagueFixtures, $competitionId, $startDate, $roundLength = 10)
    {
        $matchFactory = new MatchFactory();
        $countRound   = $roundLength;

        foreach ($leagueFixtures as $fixture) {
            $nextWeek = $countRound % $roundLength == 0;

            $matchFactory->make(
                $competitionId,
                $fixture->homeTeamId,
                $fixture->awayTeamId,
                $nextWeek ? $startDate->addWeek() : $startDate
            );

            $countRound++;
        }
    }
}
