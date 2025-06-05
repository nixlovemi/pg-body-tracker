<?php

namespace App\Helpers\Report;

use App\Helpers\Icons;
use App\Models\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Helpers\SysUtils;

class EvaluationFrequency extends ReportAbstract
{
    public function premiumOnly(): bool
    {
        return true;
    }

    public function getIcon(): string
    {
        return Icons::CALENDAR_ALT;
    }

    public function getTitle(): string
    {
        return __('messages.pages.report.EvaluationFrequency.title');
    }

    public function getDescription(): string
    {
        return __('messages.pages.report.EvaluationFrequency.description');
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
            ->whereHas('avaliations', fn ($q) => $q, '>=', 2)
            ->with(['avaliations' => fn($q) => $q->orderBy('date', 'asc')])
            ->orderByRaw("CONCAT(first_name, ' ', last_name)")
            ->get();
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
            ReportColumns::clientAvaliationsCount(),
            ReportColumns::clientAvgDaysBtwAvaliations(),
            ReportColumns::clientLastAvaliationDate(),
        ];
    }
}
