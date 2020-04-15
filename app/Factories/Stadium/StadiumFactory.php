<?php

namespace App\Factories\Stadium;

use App\Models\Stadium\Stadium;

class StadiumFactory
{
    /**
     * StadiumFactory constructor.
     *
     * @param $name
     * @param $gameId
     * @param $countryCode
     * @param $cityId
     * @param $capacity
     */
    public function __construct(
        $name, $gameId, $countryCode, $cityId, $capacity
    ) {
        $this->make($name, $gameId, $countryCode, $cityId, $capacity);
    }

    /**
     * @param $name
     * @param $gameId
     * @param $countryCode
     * @param $cityId
     * @param $capacity
     *
     * @return Stadium|bool
     */
    private function make($name, $gameId, $countryCode, $cityId, $capacity)
    {
        try {
            $stadium = new Stadium();

            $stadium->name         = $name;
            $stadium->game_id      = $gameId;
            $stadium->country_code = $countryCode;
            $stadium->city_id      = $cityId;
            $stadium->capacity     = $capacity;

            if ($stadium->save()) {
                return $stadium;
            }
        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }

        return false;
    }
}