<?php

namespace App\Tables\Helpers;

use App\Models\Client;
use Okipa\LaravelTable\Column;
use App\Helpers\SysUtils;

abstract class ClientsTableHelper
{
    public static function getLastAvaliationColumn(): Column
    {
        return Column::make('')
            ->title(__('messages.pages.avaliation.modalSelectClient.lastAvaliationColumn'))
            ->format(function(Client $Client) {
                $lastAvaliation = $Client->getLastAvaliation();
                if ($lastAvaliation) {
                    return SysUtils::reformatDate($lastAvaliation->date, 'Y-m-d', __('messages.dateFormat'));
                }
                return '';
            });
    }

    public static function getCurrentGoalDeadlineColumn(): Column
    {
        return Column::make('')
            ->title(__('messages.components.DashCardClientsWithGoalsDueThisWeek.tableColGoalDeadline'))
            ->format(function(Client $Client) {
                $currentGoal = $Client->getCurrentGoal();
                if ($currentGoal && $currentGoal->deadline) {
                    return SysUtils::reformatDate($currentGoal->deadline, 'Y-m-d', __('messages.dateFormat'));
                }
                return '';
            });
    }
}
