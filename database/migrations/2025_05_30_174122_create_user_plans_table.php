<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Helpers\Feature\FeatureAbstract;

class CreateUserPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->enum('plan_type', [FeatureAbstract::FEATURE_PLAN_TYPE_FREE, FeatureAbstract::FEATURE_PLAN_TYPE_PREMIUM])
                ->default(FeatureAbstract::FEATURE_PLAN_TYPE_FREE);
            $table->date('start_date');
            $table->date('end_date');
            $table->json('payment_data')->nullable();
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
        Schema::table('user_plans', function (Blueprint $table) {
            $table->dropUnique('uk_user_plans_user_id');
        });
        Schema::dropIfExists('user_plans');
    }
}
