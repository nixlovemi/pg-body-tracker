<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Goal;

class CreateGoalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')
                ->constrained('clients')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->enum('objective', array_keys(Goal::fGetObjectivies()))->nullable();
            $table->smallInteger('target_weight', false, true); # in kg
            $table->date('deadline');
            $table->timestamps();

            $table->unique(
                ['client_id', 'deadline'],
                'uk_goals_client_id_deadline'
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
            ALTER TABLE goals DROP FOREIGN KEY goals_client_id_foreign;
        ");
        Schema::table('goals', function (Blueprint $table) {
            $table->dropUnique('uk_goals_client_id_deadline');
        });
        Schema::dropIfExists('goals');
    }
}
