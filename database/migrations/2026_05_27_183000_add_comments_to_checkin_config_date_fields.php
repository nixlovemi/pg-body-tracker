<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddCommentsToCheckinConfigDateFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE checkin_configs MODIFY next_checkin_date DATE NULL COMMENT 'Next scheduled check-in date based on interval rules.'");
            DB::statement("ALTER TABLE checkin_configs MODIFY last_checkin_date DATE NULL COMMENT 'Last date a check-in was answered by the client.'");
            DB::statement("ALTER TABLE checkin_configs MODIFY last_checkin_sent_date DATE NULL COMMENT 'Last date a check-in link was sent to the client.'");
            return;
        }

        if ($driver === 'pgsql') {
            DB::statement("COMMENT ON COLUMN checkin_configs.next_checkin_date IS 'Next scheduled check-in date based on interval rules.'");
            DB::statement("COMMENT ON COLUMN checkin_configs.last_checkin_date IS 'Last date a check-in was answered by the client.'");
            DB::statement("COMMENT ON COLUMN checkin_configs.last_checkin_sent_date IS 'Last date a check-in link was sent to the client.'");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE checkin_configs MODIFY next_checkin_date DATE NULL COMMENT ''");
            DB::statement("ALTER TABLE checkin_configs MODIFY last_checkin_date DATE NULL COMMENT ''");
            DB::statement("ALTER TABLE checkin_configs MODIFY last_checkin_sent_date DATE NULL COMMENT ''");
            return;
        }

        if ($driver === 'pgsql') {
            DB::statement("COMMENT ON COLUMN checkin_configs.next_checkin_date IS NULL");
            DB::statement("COMMENT ON COLUMN checkin_configs.last_checkin_date IS NULL");
            DB::statement("COMMENT ON COLUMN checkin_configs.last_checkin_sent_date IS NULL");
        }
    }
}
