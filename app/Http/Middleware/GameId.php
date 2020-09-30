<?php

namespace App\Http\Middleware;

use App\Exceptions\Game\GameIdException;
use Closure;

class GameId
{
    /**
     * @param         $request
     * @param Closure $next
     *
     * @return mixed
     * @throws GameIdException
     */
    public function handle($request, Closure $next)
    {
        if ($request->headers->has('gameId') == false) {
            throw new GameIdException();
        }

        return $next($request);
    }
}
