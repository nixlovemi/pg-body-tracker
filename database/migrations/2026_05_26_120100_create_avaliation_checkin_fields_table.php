<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAvaliationCheckinFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('avaliation_checkin_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('avaliation_id')
                ->constrained('avaliations')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->string('field_class', 255);
            $table->string('field_type', 100);
            $table->string('field_key', 120);
            $table->text('response')->nullable();
            $table->string('response_type', 50);
            $table->json('field_meta')->nullable();
            $table->timestamps();

            $table->unique(['avaliation_id', 'field_key'], 'uk_avaliation_checkin_fields_avaliation_field_key');
            $table->index('field_key', 'idx_avaliation_checkin_fields_field_key');
            $table->index('field_type', 'idx_avaliation_checkin_fields_field_type');
            $table->index(['field_key', 'response_type'], 'idx_avaliation_checkin_fields_key_response_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('avaliation_checkin_fields', function (Blueprint $table) {
            $table->dropUnique('uk_avaliation_checkin_fields_avaliation_field_key');
            $table->dropIndex('idx_avaliation_checkin_fields_field_key');
            $table->dropIndex('idx_avaliation_checkin_fields_field_type');
            $table->dropIndex('idx_avaliation_checkin_fields_key_response_type');
        });

        Schema::dropIfExists('avaliation_checkin_fields');
    }
}
