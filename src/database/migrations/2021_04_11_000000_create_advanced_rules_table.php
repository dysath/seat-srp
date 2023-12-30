<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdvancedRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('denngarr_seat_srp_advrule', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->string('rule_type');
            $table->integer('type_id')->nullable()->unique();
            $table->integer('group_id')->nullable()->unique();
            $table->string('price_source')->default('internal');
            $table->bigInteger('base_value')->default(0);
            $table->integer('hull_percent')->default(0);
            $table->integer('fit_percent')->default(0);
            $table->integer('cargo_percent')->default(0);
            $table->boolean('deduct_insurance')->default(false);
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
        Schema::drop('denngarr_seat_srp_advrule');
    }
}
