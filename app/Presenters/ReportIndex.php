<?php

namespace App\Presenters;

final class ReportIndex
{
    public static function getReportCardData(): array
    {
        $data = [
            'App\Helpers\Report\OverdueEvaluations',
        ];

        return $data;
    }
}
