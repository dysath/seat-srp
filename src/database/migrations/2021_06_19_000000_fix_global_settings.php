<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class FixGlobalSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::update("update global_settings set name = 'denngarr_seat_srp_webhook_url' where name= ?", ['webhook_url']);
        DB::update("update global_settings set name = 'denngarr_seat_srp_mention_role' where name= ?", ['mention_role']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::update("update global_settings set name = 'webhook_url' where name= ?", ['denngarr_seat_srp_webhook_url']);
        DB::update("update global_settings set name = 'mention_role' where name= ?", ['denngarr_seat_srp_mention_role']);
    }
}
