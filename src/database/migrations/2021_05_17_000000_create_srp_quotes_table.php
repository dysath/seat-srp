<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSrpQuotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('denngarr_seat_quotes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('killmail_id')->unique();
            $table->integer('user')->unsigned();
            $table->float('value');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('denngarr_seat_quotes');
    }
}
