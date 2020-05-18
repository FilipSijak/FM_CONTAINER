<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('game_id');
            $table->integer('competition_id');
            $table->integer('hometeam_id')->unsigned();
            $table->integer('awayteam_id')->unsigned();
            $table->integer('stadium_id')->unsigned();
            $table->integer('attendance');
            $table->dateTime('match_start')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('matches');
    }
}
