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
            'App\Helpers\Report\ClientProgress',
            'App\Helpers\Report\ResultsComparison',
            'App\Helpers\Report\EvolutionRanking',
            'App\Helpers\Report\ClientsWithoutGoals',
        ];

        // order by premiumOnly() false first, then by getTitle()
        usort($data, function ($a, $b) {
            $aInstance = new $a();
            $bInstance = new $b();

            // Sort by premiumOnly first
            if ($aInstance->premiumOnly() !== $bInstance->premiumOnly()) {
                return $aInstance->premiumOnly() ? 1 : -1;
            }

            // Then sort by title
            return strcmp($aInstance->getTitle(), $bInstance->getTitle());
        });

        return $data;
    }
}
