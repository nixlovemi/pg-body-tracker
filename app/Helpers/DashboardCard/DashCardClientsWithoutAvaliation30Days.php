<?php

namespace App\Helpers\DashboardCard;

use App\Helpers\Icons;
use App\Models\Client;

class DashCardClientsWithoutAvaliation30Days extends CardAbstract
{
    private int $value;

    public function __construct()
    {
        $this->value = Client::fGetNbrClientsWithoutAvaliation30Days();
    }

    public function getTitle(): string
    {
        return __('messages.components.DashCardClientsWithoutAvaliation30Days.title');
    }

    public function getIcon(): string
    {
        return Icons::USER_CLOCK;
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
        return route('app.dashboard.clientsWithoutAvaliation30Days');
    }
}
