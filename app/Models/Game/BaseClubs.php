<?php

namespace App\Models\Game;

use Illuminate\Database\Eloquent\Model;

class BaseClubs extends Model
{
    protected $table      = 'base_clubs';
    public    $timestamps = false;

    public function competitions()
    {
        // base clubs will have initial competition as their main league
        // this resource is reused for game clubs which don't have this, so I query for the competitions below
        if ($this->competiton_id) {
            return $this->competiton_id;
        }

        return \DB::select("SELECT * FROM season_competition WHERE club_id = " . $this->id);
    }
}
