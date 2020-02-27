<?php

namespace App\Models\Player;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    public $timestamps = false;

    public function players()
    {
        return $this->belongsToMany(Player::class);
    }
}
