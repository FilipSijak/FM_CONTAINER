<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('game_id');
            $table->integer('club_source');
            $table->integer('club_target');
            $table->integer('transfer_type')->default(1);
            $table->integer('offer_start');
            $table->date('offer_start');
            $table->date('offer_end');
            $table->integer('status')->default(1);
            $table->integer('value')->nullable();
            $table->integer('asking_value')->nullable();
            $table->integer('fk_additions')->nullable();
            $table->date('transfer_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transfers');
    }
}
