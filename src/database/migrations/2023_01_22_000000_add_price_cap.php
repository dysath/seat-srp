<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;

class AddPriceCap extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('denngarr_seat_srp_advrule', function (Blueprint $table) {
            $table->integer('srp_price_cap')->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('denngarr_seat_srp_advrule', function (Blueprint $table) {
            $table->dropColumn('srp_price_cap');
        });
    }
}
