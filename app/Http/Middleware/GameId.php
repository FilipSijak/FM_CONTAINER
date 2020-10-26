<?php

namespace App\Http\Middleware;

use App\Exceptions\Game\GameHashException;
use App\Exceptions\Game\GameIdException;
use App\Exceptions\Game\UnallowedGameException;
use Closure;
use Illuminate\Support\Facades\DB;

class GameId
{
    /**
     * @param         $request
     * @param Closure $next
     *
     * @return mixed
     * @throws GameHashException
     * @throws GameIdException
     * @throws UnallowedGameException
     */
    public function handle($request, Closure $next)
    {
        $gameId   = $request->headers->get('gameId');
        $gameHash = $request->headers->get('gameHash');

        if (!$gameId) {
            throw new GameIdException();
        } elseif (!$gameHash) {
            throw new GameHashException();
        }

        $gamesForUser = DB::select(
            "
                SELECT
                *
                FROM games
                WHERE id = :gameId
                AND game_hash = :gameHash
            ",
            [
                'gameHash' => $gameHash,
                'gameId'   => $gameId,
            ]
        );

        if (empty($gamesForUser)) {
            throw new UnallowedGameException();
        }

        return $next($request);
    }
}
