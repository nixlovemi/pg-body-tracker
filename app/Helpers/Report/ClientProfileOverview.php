<?php

namespace App\Helpers\Report;

use App\Helpers\Icons;
use App\Models\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Helpers\SysUtils;

class ClientProfileOverview extends ReportAbstract
{
    public function getIcon(): string
    {
        return Icons::USER_FRIENDS;
    }

    public function getTitle(): string
    {
        return __('messages.pages.report.ClientProfileOverview.title');
    }

    public function getDescription(): string
    {
        return __('messages.pages.report.ClientProfileOverview.description');
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
            ->with(['avaliations' => function ($query) {
                $query->latest('date')->limit(1);
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
            ReportColumns::clientGender(),
            ReportColumns::clientAge(),
            ReportColumns::clientHeight(),
            ReportColumns::clientWeight(),
            ReportColumns::clientBmi(),
            ReportColumns::clientLastBodyFat(),
            ReportColumns::clientLastAvaliationDate(),
        ];
    }
}
