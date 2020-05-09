<?php

namespace Services\CompetitionService\League;

use Illuminate\Database\Eloquent\Collection;

class CreateLeague
{
    /**
     * @var int
     */
    protected $clubs;

    public function __construct(Collection $clubs)
    {
        $this->clubs = $clubs;
        $this->size  = $clubs->count();
    }

    /**
     * @return array
     */
    public function make(): array
    {
        $games = [];

        for ($i = 0; $i < $this->size; $i++) {
            for ($k = 0; $k < $this->size; $k++) {
                if ($k == $i) {
                    continue;
                }

                $game               = new \stdClass();
                $game->homeTeamId   = $this->clubs[$i]->id;
                $game->homeTeamName = $this->clubs[$i]->name;
                $game->awayTeamId   = $this->clubs[$k]->id;
                $game->awayTeamName = $this->clubs[$k]->name;

                $games[] = $game;
            }
        }

        return $games;
    }
}
