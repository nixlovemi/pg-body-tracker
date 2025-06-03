<?php

namespace App\Helpers\Report;

use App\Helpers\Icons;
use App\Models\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Okipa\LaravelTable\Column;
use App\Helpers\SysUtils;

class GoalsNearDeadline extends ReportAbstract
{
    public function getIcon(): string
    {
        return Icons::HOURGLASS_HALF;
    }

    public function getTitle(): string
    {
        return __('messages.pages.report.GoalsNearDeadline.title');
    }

    public function getDescription(): string
    {
        return __('messages.pages.report.GoalsNearDeadline.description');
    }

    public function getModel(): Model
    {
        return new Client();
    }

    public function applyFilter(Builder &$query)
    {
        $User = SysUtils::getLoggedInUser();
        $userId = $User->id;
        $today = SysUtils::applyTimezone(now());
        $deadlineLimit = SysUtils::applyTimezone(now())->addDays(7);

        $query->where('user_id', $userId)
            ->whereHas('goals', function ($query) use ($today, $deadlineLimit) {
                $query->whereBetween('deadline', [$today, $deadlineLimit]);
            })
            ->with(['goals' => function ($query) use ($today, $deadlineLimit) {
                $query->whereBetween('deadline', [$today, $deadlineLimit])
                    ->orderBy('deadline')
                    ->limit(1);
            }])
            ->orderBy('first_name')
            ->orderBy('last_name');
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

            Column::make('goal')
                ->title(__('messages.models.Goal.name'))
                ->format(function(Model $Model) {
                    $Goal = $Model->getCurrentGoal();
                    if (!$Goal) {
                        return '';
                    }

                    return $Goal->getObjectivieString();
                }),

            Column::make('initial_weight')
                ->title(__('messages.models.Goal.fields.initial_weight'))
                ->format(function(Model $Model) {
                    $goal = $Model->getCurrentGoal();
                    return $goal->getFormattedInitialWeight();
                }),

            Column::make('target_weight')
                ->title(__('messages.models.Goal.fields.target_weight'))
                ->format(function(Model $Model) {
                    $goal = $Model->getCurrentGoal();
                    return $goal->getFormattedTargetWeight();
                }),

            Column::make('current_weight')
                ->title(__('messages.pages.client.register.labelActualWeight'))
                ->format(function(Model $Model) {
                    return $Model->getFormattedCurrentWeight();
                }),

            Column::make('deadline')
                ->title(__('messages.models.Goal.fields.deadline'))
                ->format(function(Model $Model) {
                    $goal = $Model->getCurrentGoal();
                    return SysUtils::applyTimezone($goal->deadline)->format(__('messages.dateFormat'));
                }),

            Column::make('progress')
                ->title(__('messages.pages.goal.modalAddGoal.labelProgress'))
                ->format(function(Model $Model) {
                    $goal = $Model->getCurrentGoal();
                    return round($goal->percentageTowardsGoal(), 2) . '%';
                }),

            Column::make('remaining_days')
                ->title(__('messages.pages.goal.modalAddGoal.labelDaysToDeadline'))
                ->format(function(Model $Model) {
                    $goal = $Model->getCurrentGoal();
                    return '<div class="text-center">' . $goal->remainingDays() . '</div>';
                }),
        ];
    }
}
