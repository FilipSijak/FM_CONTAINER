<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Services\PlayerService\PlayerService;

class TestController extends Controller
{
    public function index()
    {
        $playerService = new PlayerService();
        $playerService->createPlayer();
    }
}
