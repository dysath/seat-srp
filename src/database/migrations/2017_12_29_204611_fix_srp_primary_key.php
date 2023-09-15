<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixSrpPrimaryKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('seat_srp_srp', function (Blueprint $table): void {

            $table->dropPrimary(['user_id', 'kill_id']);
            $table->primary('kill_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('seat_srp_srp', function (Blueprint $table): void {

        });
    }
}
