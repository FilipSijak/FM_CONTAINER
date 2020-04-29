<?php

namespace App\Models\Club;

use Illuminate\Database\Eloquent\Model;

class Balance extends Model
{
    /**
     * @var string
     */
    protected $table = 'balances';

    /**
     * @var bool
     */
    public $timestamps = false;

    public function club()
    {
        return $this->belongsTo(Club::class);
    }
}
