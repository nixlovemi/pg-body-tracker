<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateCheckinConfigTemporalComments extends Migration
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
            DB::statement("ALTER TABLE checkin_configs MODIFY next_checkin_date DATE NULL COMMENT 'Next scheduled check-in date for the regular cycle.'");
            DB::statement("ALTER TABLE checkin_configs MODIFY last_checkin_date DATE NULL COMMENT 'Date when the client last answered a check-in.'");
            DB::statement("ALTER TABLE checkin_configs MODIFY last_checkin_sent_date DATE NULL COMMENT 'Calendar date when the last check-in email was sent.'");
            DB::statement("ALTER TABLE checkin_configs MODIFY last_checkin_sent_at DATETIME NULL COMMENT 'Exact timestamp when the last check-in email was sent.'");
            return;
        }

        if ($driver === 'pgsql') {
            DB::statement("COMMENT ON COLUMN checkin_configs.next_checkin_date IS 'Next scheduled check-in date for the regular cycle.'");
            DB::statement("COMMENT ON COLUMN checkin_configs.last_checkin_date IS 'Date when the client last answered a check-in.'");
            DB::statement("COMMENT ON COLUMN checkin_configs.last_checkin_sent_date IS 'Calendar date when the last check-in email was sent.'");
            DB::statement("COMMENT ON COLUMN checkin_configs.last_checkin_sent_at IS 'Exact timestamp when the last check-in email was sent.'");
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
            DB::statement("ALTER TABLE checkin_configs MODIFY last_checkin_sent_at DATETIME NULL COMMENT ''");
            return;
        }

        if ($driver === 'pgsql') {
            DB::statement("COMMENT ON COLUMN checkin_configs.next_checkin_date IS NULL");
            DB::statement("COMMENT ON COLUMN checkin_configs.last_checkin_date IS NULL");
            DB::statement("COMMENT ON COLUMN checkin_configs.last_checkin_sent_date IS NULL");
            DB::statement("COMMENT ON COLUMN checkin_configs.last_checkin_sent_at IS NULL");
        }
    }
}
