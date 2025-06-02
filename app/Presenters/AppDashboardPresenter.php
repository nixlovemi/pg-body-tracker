<?php

namespace App\Presenters;

use App\Helpers\Feature\RevaluationDate;

final class AppDashboardPresenter
{
    public static function getDashboardCardData(): array
    {
        $data = [
            'App\Helpers\DashboardCard\DashCardMonthAvaliations',
            'App\Helpers\DashboardCard\DashCardMonthClients',
            'App\Helpers\DashboardCard\DashCardClientsWithoutAvaliation30Days',
            'App\Helpers\DashboardCard\DashCardClientsWithGoalsDueThisWeek',
            'App\Helpers\DashboardCard\DashCardBirthdaysMonth',
        ];

        $RevDateFeature = new RevaluationDate();
        if ($RevDateFeature->validate()) {
            $data[] = 'App\Helpers\DashboardCard\DashRevaluationsMonth';
        }

        return $data;
    }
}
