<?php

namespace App\Models\Player;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    public $timestamps = false;

    public function positions()
    {
        return $this->belongsToMany('App\Models\Player\Position');
    }
}