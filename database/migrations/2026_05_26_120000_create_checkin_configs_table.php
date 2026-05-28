<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCheckinConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('checkin_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')
                ->constrained('clients')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->boolean('active')->default(true);
            $table->unsignedSmallInteger('interval_days')->default(7);
            $table->unsignedTinyInteger('link_expires_hours')->default(24);
            $table->json('fields_config')->nullable();
            $table->date('next_checkin_date')->nullable();
            $table->date('last_checkin_date')->nullable();
            $table->timestamps();

            $table->unique('client_id', 'uk_checkin_configs_client_id');
            $table->index(['active', 'next_checkin_date'], 'idx_checkin_configs_active_next_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('checkin_configs', function (Blueprint $table) {
            $table->dropUnique('uk_checkin_configs_client_id');
            $table->dropIndex('idx_checkin_configs_active_next_date');
        });

        Schema::dropIfExists('checkin_configs');
    }
}
