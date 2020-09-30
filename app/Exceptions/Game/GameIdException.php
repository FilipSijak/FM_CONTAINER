<?php

namespace App\Exceptions\Game;

use Exception;
use Illuminate\Support\Facades\Log;

class GameIdException extends Exception
{
    public function report()
    {
        Log::debug('Missing Game ID');
    }
}
