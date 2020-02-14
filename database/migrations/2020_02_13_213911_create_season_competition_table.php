<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSeasonCompetitionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('season_competition', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('season_id')->unsigned();
            $table->integer('competition_id')->unsigned();
            $table->integer('game_id')->unsigned();
            $table->integer('club_id')->unsigned();

/*            $table->foreign('season_id')->references('id')->on('seasons');
            $table->foreign('competition_id')->references('id')->on('competitions');
            $table->foreign('game_id')->references('id')->on('games');
            $table->foreign('club_id')->references('id')->on('clubs');*/
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('season_competition');
    }
}
