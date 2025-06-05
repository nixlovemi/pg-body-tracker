<?php

namespace App\Helpers\Report;

use App\Helpers\Icons;
use App\Models\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Helpers\SysUtils;

class ClientProgress extends ReportAbstract
{
    public function premiumOnly(): bool
    {
        return true;
    }

    public function getIcon(): string
    {
        return Icons::FILE_CHART;
    }

    public function getTitle(): string
    {
        return __('messages.pages.report.ClientProgress.title');
    }

    public function getDescription(): string
    {
        return __('messages.pages.report.ClientProgress.description');
    }

    public function getModel(): Model
    {
        return new Client();
    }

    public function applyFilter(Builder &$query)
    {
        $User = SysUtils::getLoggedInUser();
        $userId = $User->id;

        $query->where('user_id', $userId)
            ->whereHas('avaliations', function ($query) {
                // can apply additional filters here if needed
            }, '>=', 2)
            ->with(['avaliations' => function ($query) {
                $query->orderBy('date', 'asc');
            }])
            ->orderByRaw("CONCAT(first_name, ' ', last_name)");
    }

    /**
     * Returns the columns to be displayed in the report.
     *
     * @return array[Okipa\LaravelTable\Column]
     */
    public function getColumns(): array
    {
        return [
            ReportColumns::clientFullName(),
            ReportColumns::clientFirstAvaliationDate(),
            ReportColumns::clientLastAvaliationDate(),
            ReportColumns::clientWeightFirstLast(),
            ReportColumns::clientBodyFatFirstLast(),
            ReportColumns::clientMuscleMassPercFirstLast(),
            ReportColumns::clientBasalMetabolismFirstLast(),
            ReportColumns::clientAvaliationsCount(),
        ];
    }
}
