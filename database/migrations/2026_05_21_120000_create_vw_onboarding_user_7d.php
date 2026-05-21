<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateVwOnboardingUser7d extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("DROP VIEW IF EXISTS vw_onboarding_user_7d");

        DB::statement("
            CREATE VIEW vw_onboarding_user_7d AS
            WITH first_client AS (
                SELECT
                    c.user_id,
                    MIN(c.created_at) AS first_client_at
                FROM clients c
                GROUP BY c.user_id
            ),
            first_avaliation AS (
                SELECT
                    c.user_id,
                    MIN(a.date) AS first_avaliation_at
                FROM avaliations a
                INNER JOIN clients c ON c.id = a.client_id
                GROUP BY c.user_id
            )
            SELECT
                u.id AS user_id,
                u.email,
                u.created_at AS signup_at,
                fc.first_client_at,
                fa.first_avaliation_at,
                CASE
                    WHEN fc.first_client_at IS NOT NULL
                        AND fc.first_client_at <= DATE_ADD(u.created_at, INTERVAL 7 DAY)
                    THEN 1 ELSE 0
                END AS first_client_7d,
                CASE
                    WHEN fa.first_avaliation_at IS NOT NULL
                        AND fa.first_avaliation_at <= DATE_ADD(u.created_at, INTERVAL 7 DAY)
                    THEN 1 ELSE 0
                END AS first_avaliation_7d,
                CASE
                    WHEN fc.first_client_at IS NOT NULL
                        AND fa.first_avaliation_at IS NOT NULL
                        AND fc.first_client_at <= DATE_ADD(u.created_at, INTERVAL 7 DAY)
                        AND fa.first_avaliation_at <= DATE_ADD(u.created_at, INTERVAL 7 DAY)
                    THEN 1 ELSE 0
                END AS completed_onboarding_7d
            FROM users u
            LEFT JOIN first_client fc ON fc.user_id = u.id
            LEFT JOIN first_avaliation fa ON fa.user_id = u.id
            WHERE u.role = 'MANAGER'
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW IF EXISTS vw_onboarding_user_7d");
    }
}
