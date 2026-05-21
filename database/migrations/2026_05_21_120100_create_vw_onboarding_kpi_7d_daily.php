<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateVwOnboardingKpi7dDaily extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("DROP VIEW IF EXISTS vw_onboarding_kpi_7d_daily");

        DB::statement("
            CREATE VIEW vw_onboarding_kpi_7d_daily AS
            SELECT
                DATE(signup_at) AS signup_date,
                COUNT(*) AS managers_signed_up,
                SUM(first_client_7d) AS managers_first_client_7d,
                SUM(first_avaliation_7d) AS managers_first_avaliation_7d,
                SUM(completed_onboarding_7d) AS managers_completed_onboarding_7d,
                ROUND(100 * SUM(first_client_7d) / NULLIF(COUNT(*), 0), 2) AS pct_first_client_7d,
                ROUND(100 * SUM(first_avaliation_7d) / NULLIF(COUNT(*), 0), 2) AS pct_first_avaliation_7d,
                ROUND(100 * SUM(completed_onboarding_7d) / NULLIF(COUNT(*), 0), 2) AS pct_completed_onboarding_7d
            FROM vw_onboarding_user_7d
            GROUP BY DATE(signup_at)
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW IF EXISTS vw_onboarding_kpi_7d_daily");
    }
}
