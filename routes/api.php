<?php

use App\Http\Controllers\Game\GameController;
use App\Http\Controllers\Game\GameSetupController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(
    [
        'prefix' => 'auth'
    ],
    function () {
        Route::post('login', 'AuthController@login');
        Route::post('register', 'AuthController@register');
        Route::post('logout', 'AuthController@logout');
        Route::post('refresh', 'AuthController@refresh');
        Route::get('user-profile', 'AuthController@userProfile');
    }
);

Route::group(
    [
        'middleware' => 'auth:api',
        'prefix' => 'setup',
    ],
    function () {
        Route::get('/', [GameSetupController::class, 'index']);
        Route::get('/load', [GameSetupController::class, 'loadGame']);
        Route::get('/countries/competitions', [GameSetupController::class, 'countriesAndCompetitions']);
        Route::get('/competitions/{id}/clubs', [GameSetupController::class, 'clubsByCompetition']);
        Route::post('/store', [GameSetupController::class, 'store']);
    }
);

Route::group(
    [
        'prefix' => 'game',
        //'middleware' => ['jwt.verify', 'gameId']
    ],
    function () {
        Route::get('/news', [GameController::class, 'news']);
        Route::patch('/next-day', [GameController::class, 'nextDay']);
    }
);

Route::get('/test', 'TestController@index');
