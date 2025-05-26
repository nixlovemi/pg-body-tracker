<?php

namespace App\Helpers\DashboardCard;

use App\Helpers\Icons;
use App\Models\Client;

class DashCardMonthClients extends CardAbstract
{
    public function getTitle(): string
    {
        return __('messages.components.DashCardMonthClients.title');
    }

    public function getIcon(): string
    {
        return Icons::USERS;
    }

    public function getValue(): string
    {
        return Client::fGetNbrNewClientsThisMonth();
    }
}
