<?php

namespace App\Helpers\DashboardCard;

use App\Helpers\Icons;
use App\Models\Avaliation;

class DashCardMonthAvaliations extends CardAbstract
{
    public function getTitle(): string
    {
        return __('messages.components.DashCardMonthAvaliations.title');
    }

    public function getIcon(): string
    {
        return Icons::FILE_CHART;
    }

    public function getValue(): string
    {
        return Avaliation::fGetNbrAvaliationThisMonth();
    }
}
