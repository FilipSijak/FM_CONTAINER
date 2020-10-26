<?php

namespace App\Exceptions\Game;

use Exception;
use Illuminate\Support\Facades\Log;

class GameHashException extends Exception
{
    public function report()
    {
        Log::debug('Invalid game hash');
    }
}
