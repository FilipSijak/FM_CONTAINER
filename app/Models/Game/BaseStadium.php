<?php

namespace App\Models\Game;

use Illuminate\Database\Eloquent\Model;

/**
 * Class BaseCities
 *
 * @package App\Models\Game
 */
class BaseStadium extends Model
{
    /**
     * @var string
     */
    protected $table      = 'base_stadiums';

    /**
     * @var bool
     */
    public    $timestamps = false;
}
