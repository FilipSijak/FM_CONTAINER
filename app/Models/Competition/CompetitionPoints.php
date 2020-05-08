<?php

namespace App\Models\Competition;

use Illuminate\Database\Eloquent\Model;

class CompetitionPoints extends Model
{
    protected $table = 'competition_points';

    public $timestamps = false;

    public function competitions()
    {
        return $this->belongsToMany(Competition::class);
    }
}
