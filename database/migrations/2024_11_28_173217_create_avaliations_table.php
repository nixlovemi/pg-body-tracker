<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateAvaliationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('avaliations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')
                ->constrained('clients')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->date('date');
            $table->float('weight_kg', 8, 2, true);
            $table->smallInteger('height_cm', false, true);
            $table->float('body_fat_perc', 8, 2, true);
            $table->float('skeletal_muscle_perc', 8, 2, true)->nullable(); # used to calculate skeletal_muscle_kg
            $table->float('visceral_fat_kg', 8, 2, true)->nullable();
            $table->smallInteger('waist_circumference_cm', false, true)->nullable(); # used to calculate visceral_fat_kg

            $table->unique(
                ['client_id', 'date'],
                'uk_avaliations_client_id_date'
            );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("
            ALTER TABLE avaliations DROP FOREIGN KEY avaliations_client_id_foreign;
        ");
        Schema::table('avaliations', function (Blueprint $table) {
            $table->dropUnique('uk_avaliations_client_id_date');
        });
        Schema::dropIfExists('avaliations');
    }
}
