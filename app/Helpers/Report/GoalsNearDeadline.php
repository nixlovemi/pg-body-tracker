<?php

namespace App\Helpers\Report;

use App\Helpers\Icons;
use App\Models\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
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
            ReportColumns::clientFullName(),
            ReportColumns::clientCurrentGoalName(),
            ReportColumns::clientCurrentGoalInitialWeight(),
            ReportColumns::clientCurrentGoalTargetWeight(),
            ReportColumns::clientWeight(),
            ReportColumns::clientCurrentGoalDeadline(),
            ReportColumns::clientCurrentProgress(),
            ReportColumns::clientCurrentRemainingDays(),
        ];
    }
}
