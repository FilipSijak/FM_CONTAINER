<?php

namespace App\Exceptions\Game;

use Exception;
use Illuminate\Support\Facades\Log;

class UnallowedGameException extends Exception
{
    public function report()
    {
        Log::debug('Accessing wrong or unallowed game');
    }
}
