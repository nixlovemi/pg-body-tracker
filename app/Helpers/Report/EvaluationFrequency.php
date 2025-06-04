<?php

namespace App\Helpers\Report;

use App\Helpers\Icons;
use App\Models\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Okipa\LaravelTable\Column;
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
            Column::make('fullName')
                ->title(__('messages.models.Client.name'))
                ->format(function(Model $Model) {
                    return $Model->getName();
                }),

            Column::make('avaliations_count')
                ->title(__('messages.menu.avaliation'))
                ->format(function(Model $Model) {
                    return $Model->avaliations->count();
                }),

            Column::make('avg_days_btw_evaluations')
                ->title(__('messages.pages.report.EvaluationFrequency.columns.avgDaysBtwEvaluations'))
                ->format(function(Model $Model) {
                    return $Model->getAvgDaysBtwAvaliations();
                }),

            Column::make('last_avaliation')
                ->title(__('messages.pages.avaliation.modalSelectClient.lastAvaliationColumn'))
                ->format(function(Model $Model) {
                    return $Model->getLastAvaliation()->getFormattedDate();
                }),
        ];
    }
}
