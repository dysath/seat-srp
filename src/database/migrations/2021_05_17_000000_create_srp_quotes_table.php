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
    public function up(): void
    {
        Schema::create('denngarr_seat_quotes', function (Blueprint $table): void {
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
    public function down(): void
    {
        Schema::drop('denngarr_seat_quotes');
    }
}
