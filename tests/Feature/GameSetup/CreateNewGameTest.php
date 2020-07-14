<?php

namespace Tests\Feature\GameSetup;

use App\Models\Club\Balance;
use App\Models\Club\Club;
use App\Models\Competition\Match;
use App\Models\Game\BaseCities;
use App\Models\Game\BaseClubs;
use App\Models\Game\City;
use Tests\TestCase;

class CreateNewGameTest extends TestCase
{
    private $baseClubs;

    public function setUp(): void
    {
        parent::setUp();

        $this->baseClubs = BaseClubs::all();
    }

    public function testGameIsCreated()
    {
        $this->assertDatabaseHas('games', ['user_id' => 1]);
    }

    public function testFirstSeasonLeagueMatches()
    {
        $this->assertDatabaseHas('matches',
             [
                 'game_id' => 1,
                 'competition_id' => 1,
                 'hometeam_id' => 1,
                 'awayteam_id' => 20
             ]
        );
    }

    public function testLeagueMatchesCreated()
    {
        $matches = Match::all();

        $this->assertEquals(count($matches), 190);
    }

    public function testBalancesPopulated()
    {
        $balances = Balance::all();

        $this->assertEquals(count($balances), count($this->baseClubs));
    }

    public function testCitiesPopulated()
    {
        $cities = City::all();
        $baseCities = BaseCities::all();

        $this->assertEquals(count($cities), count($baseCities));
    }

    public function testClubsPopulated()
    {
        $clubs = Club::all();
        $baseClubs = BaseClubs::all();

        $this->assertEquals(count($clubs), count($baseClubs));
    }
}