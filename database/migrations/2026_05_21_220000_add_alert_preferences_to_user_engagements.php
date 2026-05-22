<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAlertPreferencesToUserEngagements extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_engagements', function (Blueprint $table) {
            $table->json('alert_preferences')->nullable()->after('trigger_state');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_engagements', function (Blueprint $table) {
            $table->dropColumn('alert_preferences');
        });
    }
}
