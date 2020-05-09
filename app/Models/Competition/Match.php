<?php

namespace App\Models\Competition;

use App\Models\Game\Game;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Match
 *
 * @package App\Models\Competition
 */
class Match extends Model
{
    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var string
     */
    protected $table = 'matches';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function game()
    {
        return $this->belongsToMany(Game::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function competition()
    {
        return $this->belongsToMany(Competition::class);
    }
}
