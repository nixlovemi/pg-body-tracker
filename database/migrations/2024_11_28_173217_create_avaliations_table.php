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
        /*
        Fórmulas
        ========
            - Fórmula Simplificada: Massa Muscular Esquelética (kg) = (Peso Corporal Total (kg) × Percentual Estimado de Músculos (%))
                - Homens: Geralmente, 40-50% do peso corporal é músculo.
                    - Excelente: 45-55%
                    - Bom: 40-44%
                    - Abaixo da Média: <40%
                - Mulheres: Geralmente, 30-40% do peso corporal é músculo.
                    - Excelente: 35-45%
                    - Bom: 30-34%
                    - Abaixo da Média: <30%
        */

        Schema::create('avaliations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')
                ->constrained('clients')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->date('date');
            $table->float('weight_kg', 8, 2, true);
            $table->smallInteger('height_cm', false, true);
            $table->float('body_fat_perc', 8, 2, true);
            $table->float('skeletal_muscle_mass_kg', 8, 2, true)->nullable();
            $table->float('muscle_rate_perc', 8, 2, true)->nullable();
            $table->float('subcutaneous_fat_perc', 8, 2, true)->nullable();
            $table->float('visceral_fat_perc', 8, 2, true)->nullable();
            $table->float('body_water_perc', 8, 2, true)->nullable();
            $table->float('skeletal_muscle_perc', 8, 2, true)->nullable();
            $table->float('muscle_mass_kg', 8, 2, true)->nullable();
            $table->float('bone_mass_kg', 8, 2, true)->nullable();
            $table->float('protein_perc', 8, 2, true)->nullable();

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
