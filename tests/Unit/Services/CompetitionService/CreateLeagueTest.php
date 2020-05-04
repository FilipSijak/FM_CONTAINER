<?php

namespace Tests\Unit\Services\CompetitionService;

use App\Models\Club\Club;
use Services\CompetitionService\League\CreateLeague;
use Tests\TestCase;

class CreateLeagueTest extends TestCase
{
    /**
     * @var Club[]|\Illuminate\Database\Eloquent\Collection
     */
    protected $clubs;

    public function setUp(): void
    {
        parent::setUp();

        $this->clubs = Club::all();
    }

    public function testLeagueGamesNumber()
    {
        $league              = new CreateLeague($this->clubs);
        $games               = $league->make();
        $expectedLeagueGames = $this->clubs->count() * ($this->clubs->count() - 1);

        $this->assertEquals($expectedLeagueGames, count($games));
    }
}
