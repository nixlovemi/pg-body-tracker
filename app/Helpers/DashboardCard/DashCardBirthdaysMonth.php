<?php

namespace App\Helpers\DashboardCard;

use App\Helpers\Icons;
use App\Helpers\SysUtils;

class DashCardBirthdaysMonth extends CardAbstract
{
    public function getTitle(): string
    {
        return __('messages.components.DashCardBirthdaysMonth.title');
    }

    public function getIcon(): string
    {
        return Icons::BIRTHDAY_CAKE;
    }

    public function getValue(): string
    {
        $User = SysUtils::getLoggedInUser();
        if (!$User) {
            return '0';
        }

        // Get the number of birthdays in the current month
        return $User->clients()->whereRaw('EXTRACT(MONTH FROM birthdate) = ' . date('n'))
            ->count();
    }

    public function getClickUrl(): ?string
    {
        return route('app.calendar.index');
    }
}
