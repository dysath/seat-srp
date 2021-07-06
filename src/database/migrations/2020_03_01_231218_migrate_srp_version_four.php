<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrateSrpVersionFour extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Duplicate the table into a backup for safekeeping
        DB::statement('CREATE TABLE seat_srp_srp_three LIKE seat_srp_srp');
        DB::statement('INSERT seat_srp_srp_three SELECT * FROM seat_srp_srp');

        $entries = DB::table('seat_srp_srp')->get();

        foreach ($entries as $entry) {
            $new_id = DB::table('mig_groups')
                ->where('old_user_id', $entry->user_id)
                ->first();
            if (is_null($new_id) || is_null($new_id->new_user_id)){
                DB::table('seat_srp_srp')
                    ->where('kill_id', $entry->kill_id)
                    ->delete();
                continue;
            }

            DB::table('seat_srp_srp')
                ->where('user_id', $entry->user_id)
                ->update(['user_id' => $new_id->new_user_id]);
        }

        Schema::table('seat_srp_srp', function (Blueprint $table) {

            $table->unsignedInteger('user_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('seat_srp_srp');
        Schema::rename('seat_srp_srp_three', 'seat_srp_srp');
    }
}
