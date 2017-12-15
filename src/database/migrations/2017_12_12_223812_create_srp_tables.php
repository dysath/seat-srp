<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSrpTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seat_srp_srp', function (Blueprint $table) {
            $table->integer('user_id');
            $table->string('character_name');
            $table->integer('kill_id');
            $table->string('kill_token');
            $table->integer('approved');
            $table->double('cost');
            $table->string('ship_type');
            $table->timestamps();
            $table->primary(['user_id', 'kill_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seat_srp_srp');
    }
}
