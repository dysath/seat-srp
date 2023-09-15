<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class FixGlobalSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        DB::update("update global_settings set name = 'denngarr_seat_srp_webhook_url' where name= ?", ['webhook_url']);
        DB::update("update global_settings set name = 'denngarr_seat_srp_mention_role' where name= ?", ['mention_role']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        DB::update("update global_settings set name = 'webhook_url' where name= ?", ['denngarr_seat_srp_webhook_url']);
        DB::update("update global_settings set name = 'mention_role' where name= ?", ['denngarr_seat_srp_mention_role']);
    }
}
