<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Avaliation;

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
            $table->smallInteger('age', false, true);
            $table->smallInteger('height_cm', false, true);

            $table->float('weight_kg', 5, 1, true);
            $table->enum('calculate_perc_fat_by', array_keys(Avaliation::fGetCalculatePercFatBy()));

            $table->float('body_fat_perc', 5, 2, true)->nullable();
            $table->float('skeletal_muscle_perc', 5, 2, true)->nullable(); # used to calculate skeletal_muscle_kg
            $table->float('muscle_mass_perc', 5, 2, true)->nullable();
            $table->float('visceral_fat_kg', 5, 1, true)->nullable();
            $table->smallInteger('basal_metabolism', false, true)->nullable();
            $table->smallInteger('body_age', false, true)->nullable();
            $table->float('body_water_perc', 5, 2, true)->nullable();
            $table->float('bone_mass_kg', 5, 1, true)->nullable();

            $table->float('right_arm_lean_mass_kg', 5, 1, true)->nullable();
            $table->float('right_arm_lean_mass_perc', 5, 2, true)->nullable();
            $table->float('right_arm_fat_kg', 5, 1, true)->nullable();
            $table->float('right_arm_fat_perc', 5, 2, true)->nullable();
            $table->float('left_arm_lean_mass_kg', 5, 1, true)->nullable();
            $table->float('left_arm_lean_mass_perc', 5, 2, true)->nullable();
            $table->float('left_arm_fat_kg', 5, 1, true)->nullable();
            $table->float('left_arm_fat_perc', 5, 2, true)->nullable();
            $table->float('trunk_lean_mass_kg', 5, 1, true)->nullable();
            $table->float('trunk_lean_mass_perc', 5, 2, true)->nullable();
            $table->float('trunk_fat_kg', 5, 1, true)->nullable();
            $table->float('trunk_fat_perc', 5, 2, true)->nullable();
            $table->float('right_leg_lean_mass_kg', 5, 1, true)->nullable();
            $table->float('right_leg_lean_mass_perc', 5, 2, true)->nullable();
            $table->float('right_leg_fat_kg', 5, 1, true)->nullable();
            $table->float('right_leg_fat_perc', 5, 2, true)->nullable();
            $table->float('left_leg_lean_mass_kg', 5, 1, true)->nullable();
            $table->float('left_leg_lean_mass_perc', 5, 2, true)->nullable();
            $table->float('left_leg_fat_kg', 5, 1, true)->nullable();
            $table->float('left_leg_fat_perc', 5, 2, true)->nullable();

            $table->float('chest_circ_cm', 5, 1, true)->nullable();
            $table->float('right_arm_circ_cm', 5, 1, true)->nullable();
            $table->float('left_arm_circ_cm', 5, 1, true)->nullable();
            $table->float('waist_circ_cm', 5, 1, true)->nullable();
            $table->float('right_forearm_circ_cm', 5, 1, true)->nullable();
            $table->float('left_forearm_circ_cm', 5, 1, true)->nullable();
            $table->float('abdomen_circ_cm', 5, 1, true)->nullable();
            $table->float('right_thigh_circ_cm', 5, 1, true)->nullable();
            $table->float('left_thigh_circ_cm', 5, 1, true)->nullable();
            $table->float('hip_circ_cm', 5, 1, true)->nullable();
            $table->float('right_calf_circ_cm', 5, 1, true)->nullable();
            $table->float('left_calf_circ_cm', 5, 1, true)->nullable();
            $table->float('neck_circ_cm', 5, 1, true)->nullable();

            $table->enum('skin_folds_formula', array_keys(Avaliation::fGetSkinFoldFormulas()))->nullable();
            $table->float('skin_folds_chest_cm', 5, 1, true)->nullable();
            $table->float('skin_folds_abdominal_cm', 5, 1, true)->nullable();
            $table->float('skin_folds_thigh_cm', 5, 1, true)->nullable();
            $table->float('skin_folds_tricep_cm', 5, 1, true)->nullable();
            $table->float('skin_folds_suprailiac_cm', 5, 1, true)->nullable();
            $table->float('skin_folds_axilla_cm', 5, 1, true)->nullable();
            $table->float('skin_folds_subscapular_cm', 5, 1, true)->nullable();
            $table->float('skin_folds_bicep_cm', 5, 1, true)->nullable();

            $table->text('client_notes')->nullable();
            $table->text('private_notes')->nullable();

            $table->string('photo_front_url', 200)->nullable();
            $table->string('photo_right_url', 200)->nullable();
            $table->string('photo_rear_url', 200)->nullable();
            $table->string('photo_left_url', 200)->nullable();

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
