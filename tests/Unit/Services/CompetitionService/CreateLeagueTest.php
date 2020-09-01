<?php

namespace Tests\Unit\Services\CompetitionService;

use App\Models\Club\Club;
use Services\CompetitionService\League\League;
use Tests\TestCase;

class CreateLeagueTest extends TestCase
{
    protected $clubs = [];

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testLeagueGamesNumber()
    {
        for ($i = 1; $i <= 20; $i++) {
            $club = new \stdClass();
            $club->id = $i;
            $this->clubs[] = $club;
        }

        $league              = new League($this->clubs);
        $games               = $league->generateLeagueGames();
        $expectedLeagueGames = count($this->clubs) * (count($this->clubs) - 1);

        $this->assertEquals($expectedLeagueGames, count($games));
    }
}
