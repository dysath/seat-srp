<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigrateSrpCharacterToId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('seat_srp_srp', function (Blueprint $table) {
            $table->bigInteger('character_id')->after('user_id');
        });

        $entries = DB::table('seat_srp_srp')->get();

        foreach ($entries as $entry) {
            $char_id = DB::table('character_infos')
                ->where('name', $entry->character_name)
                ->first();
            if (is_null($char_id) || is_null($char_id->character_id)){
                // This name does not exist, 
                DB::table('seat_srp_srp')
                    ->where('kill_id', $entry->kill_id)
                    ->update(['character_id' => $entry->character_name]);
                continue;
            }

            DB::table('seat_srp_srp')
                ->where('kill_id', $entry->kill_id)
                ->update(['character_id' => $char_id->character_id]);
        }

        Schema::table('seat_srp_srp', function (Blueprint $table) {
            $table->dropColumn('character_name');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('seat_srp_srp', function (Blueprint $table) {
            $table->dropColumn('character_id');
        });

    }
}
