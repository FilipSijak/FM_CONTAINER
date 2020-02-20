<?php

use Illuminate\Http\Request;

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

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::group(['prefix' => 'games', 'as' => 'game.'], function () {
    Route::get('/', 'Game\GameController@index');

    Route::get('/countries/competitions', 'Game\GameController@getCountriesAndCompetitions');
    Route::get('/competitions/clubs', 'Game\GameController@getClubsByCompetition');

    Route::post('/store', 'Game\GameController@store')->name('store');
});
