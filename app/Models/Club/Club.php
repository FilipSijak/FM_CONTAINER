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
        return $this->hasMany('App\Models\Player\Player', 'club_id');
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
