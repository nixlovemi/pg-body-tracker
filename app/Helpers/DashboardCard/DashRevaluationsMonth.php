<?php

namespace App\Helpers\DashboardCard;

use App\Helpers\Icons;
use App\Helpers\SysUtils;
use App\Models\Avaliation;
use App\Helpers\Feature\RevaluationDate;

class DashRevaluationsMonth extends CardAbstract
{
    public function getTitle(): string
    {
        return __('messages.components.DashRevaluationsMonth.title');
    }

    public function getIcon(): string
    {
        return Icons::CALENDAR_ALT;
    }

    public function getValue(): string
    {
        $RevDateFeature = new RevaluationDate();
        $User = SysUtils::getLoggedInUser();
        if (!$User || !$RevDateFeature->validate()) {
            return '0';
        }

        $clientIds = $User->clients->pluck('id')->map(fn($id) => (int) $id)->toArray();
        $Avaliations = Avaliation::whereNotNull('revaluation_date')
            ->whereIn('client_id', $clientIds)
            ->whereRaw('EXTRACT(MONTH FROM revaluation_date) = ' . date('n'))
            ->whereRaw('EXTRACT(YEAR FROM revaluation_date) = ' . date('Y'));

        // Get the number of revaluations in the current month
        return $Avaliations->count();
    }

    public function getClickUrl(): ?string
    {
        return route('app.calendar.index');
    }
}
