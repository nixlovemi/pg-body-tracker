<?php

namespace App\Presenters;

final class ReportIndex
{
    public static function getReportCardData(): array
    {
        $data = [
            'App\Helpers\Report\OverdueEvaluations',
            'App\Helpers\Report\GoalsNearDeadline',
            'App\Helpers\Report\ClientProfileOverview',
            'App\Helpers\Report\ClientProgress'
        ];

        return $data;
    }
}
