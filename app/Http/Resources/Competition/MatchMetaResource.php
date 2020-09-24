<?php

namespace App\Http\Resources\Competition;

use Illuminate\Http\Resources\Json\JsonResource;

class MatchMetaResource extends JsonResource
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
            'id'             => $this->id,
            'game_id'        => $this->game_id,
            'competition_id' => $this->competition_id,
            'hometeam_id'    => $this->hometeam_id,
            'awayteam_id'    => $this->awayteam_id,
            'stadium_id'     => $this->stadium_id,
            'attendance'     => $this->attendance,
            'match_start'    => $this->match_start,
        ];
    }
}
