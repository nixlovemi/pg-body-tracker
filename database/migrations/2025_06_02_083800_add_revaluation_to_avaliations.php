<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class AddRevaluationToAvaliations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('avaliations', function (Blueprint $table) {
            $table->date('revaluation_date')
                ->nullable()
                ->after('private_notes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('avaliations', function (Blueprint $table) {
            $table->dropColumn('revaluation_date');
        });
    }
}
