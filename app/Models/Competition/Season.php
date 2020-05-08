<?php

namespace App\Models\Competition;

use Illuminate\Database\Eloquent\Model;

class Season extends Model
{
    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var string
     */
    protected $table = 'seasons';

    public function competitions()
    {
        return $this->belongsToMany(Competition::class);
    }
}
