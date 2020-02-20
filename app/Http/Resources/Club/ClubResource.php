<?php

namespace App\Http\Resources\Club;

use Illuminate\Http\Resources\Json\JsonResource;

class ClubResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'country_code' => $this->country_code,
            'city_id' => $this->city_id,
            'competition_id' => $this->competition_id,
            'stadium_id' => $this->stadium_id,
            'rank' => $this->rank,
            'rank_academy' => $this->rank_academy,
            'rank_training' => $this->rank_training,
        ];
    }
}
