<?php

namespace App\Http\Resources\Game;

use Illuminate\Http\Resources\Json\JsonResource;

class CountryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'           => $this->id,
            'country_code' => $this->country_code,
            'name'         => $this->name,
            'game_id'      => $this->game_id,
            'ranking'      => $this->ranking,
            'population'   => $this->population,
            'gdp'          => $this->gdp,
            'gdpcapita'    => $this->gdpcapita,
            'continent'    => $this->continent,
        ];
    }
}
