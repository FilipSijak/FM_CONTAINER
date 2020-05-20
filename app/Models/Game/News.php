<?php

namespace App\Models\Game;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    public $timestamps = false;

    public function game()
    {
        return $this->belongsTo(Game::class, 'game_id');
    }
}
