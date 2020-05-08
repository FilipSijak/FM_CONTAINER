<?php

namespace App\Models\Competition;

use Illuminate\Database\Eloquent\Model;

class Competition extends Model
{
    /**
     * @var string
     */
    protected $table = 'competitions';

    /**
     * @var bool
     */
    public $timestamps = false;

    public function seasons()
    {
        return $this->belongsToMany(Season::class);
    }

    public function points()
    {
        return $this->hasMany(CompetitionPoints::class, 'competition_id');
    }
}
