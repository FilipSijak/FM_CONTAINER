<?php

namespace App\Http\Controllers;

use App\Models\Game\Game;
use App\Traits\GameInfo;
use Illuminate\Http\Request;

class CoreController extends Controller
{
    use GameInfo;

    protected $game = null;

    public function __construct(Request $request)
    {
        if ($request->header('gameId')) {
            $this->game = Game::find($request->header('gameId'));
        }
    }
}
