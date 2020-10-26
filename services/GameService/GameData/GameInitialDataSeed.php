<?php

namespace Services\GameService\GameData;

use App\Factories\Stadium\StadiumFactory;
use App\Models\Club\Club;
use App\Models\Competition\Competition;
use App\Models\Game\BaseCities;
use App\Models\Game\BaseStadium;
use App\Models\Game\City;
use App\Models\Game\Country;
use App\Models\Game\Game;
use App\Models\Game\BaseClubs;
use App\Models\Game\BaseCompetitions;
use App\Models\Game\BaseCountries;
use App\Models\Stadium\Stadium;
use Services\GameService\Interfaces\GameInitialDataSeedInterface;

class GameInitialDataSeed implements GameInitialDataSeedInterface
{
    /**
     * @var BaseClubs
     */
    protected $baseClubs;

    /**
     * @var BaseCountries
     */
    protected $baseCountries;

    /**
     * @var BaseCompetitions
     */
    protected $baseCompetitions;

    /**
     * @var BaseStadium
     */
    protected $baseStadium;

    /**
     * @var BaseCities
     */
    protected $baseCities;

    /**
     * GameInitialDataSeed constructor.
     *
     * @param BaseClubs        $baseClubs
     * @param BaseCountries    $baseCountries
     * @param BaseCompetitions $baseCompetitions
     * @param BaseCities       $baseCities
     * @param BaseStadium      $baseStadium
     */
    public function __construct
    (
        BaseClubs $baseClubs,
        BaseCountries $baseCountries,
        BaseCompetitions $baseCompetitions,
        BaseCities $baseCities,
        BaseStadium $baseStadium
    ) {
        $this->baseClubs        = $baseClubs;
        $this->baseCountries    = $baseCountries;
        $this->baseCompetitions = $baseCompetitions;
        $this->baseStadium      = $baseStadium;
        $this->baseCities       = $baseCities;

        return $this;
    }

    /**
     * @param Game $game
     */
    public function seedFromBaseTables(int $gameId)
    {

        foreach ($this->baseStadium::all() as $baseStadium) {
            try {
                new StadiumFactory(
                    $baseStadium->name,
                    $gameId,
                    $baseStadium->country_code,
                    $baseStadium->city_id,
                    $baseStadium->capacity
                );
            } catch (\Exception $exception) {
                echo $exception->getMessage();
            }

        }

        foreach ($this->baseCountries::all() as $baseCountry) {
            try {
                $country = new Country();

                $country->code       = $baseCountry->code;
                $country->name       = $baseCountry->name;
                $country->game_id    = $gameId;
                $country->ranking    = $baseCountry->ranking;
                $country->population = $baseCountry->population;
                $country->gdp        = $baseCountry->gdp;
                $country->gdpcapita  = $baseCountry->gdpcapita;
                $country->continent  = $baseCountry->continent;

                $country->save();
            } catch (\Exception $exception) {
                echo $exception->getMessage();
            }
        }

        foreach ($this->baseCompetitions::all() as $baseCompetition) {
            try {
                $competition = new Competition();

                $competition->name         = $baseCompetition->name;
                $competition->country_code = $baseCompetition->country_code;
                $competition->game_id      = $gameId;
                $competition->rank         = $baseCompetition->rank;
                $competition->type         = $baseCompetition->type;


                $competition->save();
            } catch (\Exception $exception) {
                echo $exception->getMessage();
            }
        }

        foreach ($this->baseClubs::all() as $baseClub) {
            try {
                $club = new Club();

                $club->name          = $baseClub->name;
                $club->game_id       = $gameId;
                $club->country_code  = $baseClub->country_code;
                $club->city_id       = $baseClub->city_id;
                $club->stadium_id    = $baseClub->stadium_id;
                $club->rank          = $baseClub->rank;
                $club->rank_academy  = $baseClub->rank_academy;
                $club->rank_training = $baseClub->rank_training;

                $club->save();
            } catch (\Exception $exception) {
                echo $exception->getMessage();
            }
        }

        foreach ($this->baseCities::all() as $baseCity) {
            try {
                $city = new City();

                $city->name         = $baseCity->name;
                $city->game_id      = $gameId;
                $city->country_code = $baseCity->country_code;
                $city->population   = $baseCity->population;

                $city->save();
            } catch (\Exception $exception) {
                echo $exception->getMessage();
            }
        }
    }
}