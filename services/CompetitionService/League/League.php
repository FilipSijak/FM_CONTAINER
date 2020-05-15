<?php

namespace Services\CompetitionService\League;

use Illuminate\Database\Eloquent\Collection;

class League
{
    protected $clubs;

    protected $games = [];

    protected $fixed;

    protected $home = true;

    protected $numberOfGamesInRound;

    public function __construct(Collection $clubs)
    {
        $this->clubs                = $clubs;
        $this->competitionSize      = count($clubs);
        $this->numberOfGamesInRound = $this->competitionSize / 2;
        $this->fixed                = !empty($clubs->first()) ? $clubs->first()->id : 0;
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
            $game->homeTeamId = $this->clubs[$i]->id;
            $game->awayTeamId = $this->clubs[$k]->id;

            $this->games[] = $game;
        }

        // Based on first round games, this will loop and set the rest of rounds
        for ($i = 0; $i < $this->competitionSize - 2; $i++) {
            $this->generateSingleRoundGames();
        }

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
}
