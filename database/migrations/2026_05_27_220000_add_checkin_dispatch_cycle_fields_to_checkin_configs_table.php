<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCheckinDispatchCycleFieldsToCheckinConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('checkin_configs', function (Blueprint $table) {
            $table->dateTime('last_checkin_sent_at')
                ->nullable()
                ->comment('Last exact timestamp when a check-in link was sent to the client.')
                ->after('last_checkin_sent_date');

            $table->unsignedTinyInteger('unanswered_reminders_sent')
                ->default(0)
                ->comment('Number of reminder emails already sent for the current unanswered check-in cycle.')
                ->after('last_checkin_sent_at');
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
            $table->dropColumn('unanswered_reminders_sent');
            $table->dropColumn('last_checkin_sent_at');
        });
    }
}
