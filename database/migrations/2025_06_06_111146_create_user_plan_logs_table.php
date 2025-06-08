<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserPlanLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_plan_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_plan_id')
                ->constrained('user_plans')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->string('payment_class', 255);
            $table->string('payment_id', 100);
            $table->json('data');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_plan_logs', function (Blueprint $table) {
            $table->dropUnique('uk_user_plan_logs_user_plan_id');
        });
        Schema::dropIfExists('user_plan_logs');
    }
}
