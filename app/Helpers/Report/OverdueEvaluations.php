<?php

namespace App\Helpers\Report;

use App\Helpers\Icons;
use App\Models\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Okipa\LaravelTable\Column;
use App\Helpers\SysUtils;
use Illuminate\Support\Facades\DB;

class OverdueEvaluations extends ReportAbstract
{
    private const CUT_OFF_DAYS = 60;

    public function getIcon(): string
    {
        return Icons::CALENDAR_TIME;
    }

    public function getTitle(): string
    {
        return __('messages.pages.report.OverdueEvaluations.title');
    }

    public function getDescription(): string
    {
        return __('messages.pages.report.OverdueEvaluations.description', [
            'days' => self::CUT_OFF_DAYS,
        ]);
    }

    public function getModel(): Model
    {
        return new Client();
    }

    public function applyFilter(Builder &$query)
    {
        $User = SysUtils::getLoggedInUser();
        $userId = $User->id;
        $cutoffDays = self::CUT_OFF_DAYS;
        $cutoffDate = SysUtils::applyTimezone(date('Y-m-d'))->subDays($cutoffDays)->format('Y-m-d');

        $sub = DB::table('avaliations')
            ->selectRaw('MAX(date)')
            ->whereColumn('client_id', 'clients.id');

        $query = $query->where('user_id', $userId)
            ->addSelect(['last_avaliation_date' => $sub])
            ->having('last_avaliation_date', '<', $cutoffDate)
            ->orderBy('last_avaliation_date', 'asc')
            ->with(['avaliations' => function ($query) {
                $query->latest('date')->limit(1);
            }]);
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

            Column::make('lastAvaliation')
                ->title(__('messages.pages.avaliation.modalSelectClient.lastAvaliationColumn'))
                ->format(function(Model $Model) {
                    return SysUtils::reformatDate($Model->getLastAvaliation()->date, 'Y-m-d', __('messages.dateFormat'));
                }),

            Column::make('dueDays')
                ->title(__('messages.pages.report.OverdueEvaluations.columns.daysOverdue'))
                ->format(function(Model $Model) {
                    $lastAvaliation = $Model->getLastAvaliation();
                    $daysOverdue = SysUtils::applyTimezone($lastAvaliation->date)->diffInDays(now());
                    return '<div class="text-center">' . $daysOverdue . '</div>';
                }),

            Column::make('email')
                ->title(__('messages.pages.client.table.colEmail'))
                ->format(function(Model $Model) {
                    return $Model->email;
                }),

            Column::make('phone')
                ->title(__('messages.pages.client.table.colPhone'))
                ->format(function(Model $Model) {
                    return $Model->phone;
                }),

            Column::make('goal')
                ->title(__('messages.models.Goal.name'))
                ->format(function(Model $Model) {
                    $Goal = $Model->getCurrentGoal();
                    if (!$Goal) {
                        return '';
                    }

                    return $Goal->getObjectivieString();
                }),

            Column::make('goal_due')
                ->title(__('messages.models.Goal.fields.deadline'))
                ->format(function(Model $Model) {
                    $Goal = $Model->getCurrentGoal();
                    if (!$Goal) {
                        return '';
                    }

                    return SysUtils::reformatDate($Goal->deadline, 'Y-m-d', __('messages.dateFormat'));
                }),
        ];
    }
}
