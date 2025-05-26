<?php

namespace App\Helpers\DashboardCard;

use App\Helpers\Icons;
use App\Models\Client;

class DashCardClientsWithGoalsDueThisWeek extends CardAbstract
{
    private int $value;

    public function __construct()
    {
        $this->value = Client::fGetNbrClientsWithGoalsDueThisWeek();
    }

    public function getTitle(): string
    {
        return __('messages.components.DashCardClientsWithGoalsDueThisWeek.title');
    }

    public function getIcon(): string
    {
        return Icons::BULLS_EYE;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getCardClass(): string
    {
        if ($this->value > 0) {
            return 'warning';
        }

        return 'primary';
    }

    public function getClickUrl(): ?string
    {
        return route('app.dashboard.clientsWithGoalsDueThisWeek');
    }
}
