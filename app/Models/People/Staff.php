<?php

namespace App\Models\People;

use App\Models\Club\Club;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    public $timestamps = false;

    public function clubs()
    {
        return $this->belongsToMany(Club::class, 'staff_club');
    }
}
