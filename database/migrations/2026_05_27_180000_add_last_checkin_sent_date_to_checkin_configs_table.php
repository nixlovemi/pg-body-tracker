<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLastCheckinSentDateToCheckinConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('checkin_configs', function (Blueprint $table) {
            $table->date('last_checkin_sent_date')->nullable()->after('last_checkin_date');
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
            $table->dropColumn('last_checkin_sent_date');
        });
    }
}
