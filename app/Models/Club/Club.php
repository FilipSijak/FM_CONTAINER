<?php

namespace App\Models\Club;

use App\Models\People\Staff;
use Illuminate\Database\Eloquent\Model;

class Club extends Model
{
    /**
     * @var string
     */
    protected $table = 'clubs';

    /**
     * @var bool
     */
    public $timestamps = false;

    public function players()
    {
        return $this->belongsToMany('App\Models\Player\Player', 'player_club');
    }

    public function balance()
    {
        return $this->hasOne(Balance::class);
    }

    public function staff()
    {
        return $this->belongsToMany(Staff::class, 'staff_club');
    }
}
