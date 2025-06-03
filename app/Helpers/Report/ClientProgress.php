<?php

namespace App\Helpers\Report;

use App\Helpers\Icons;
use App\Models\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Okipa\LaravelTable\Column;
use App\Helpers\SysUtils;

class ClientProgress extends ReportAbstract
{
    public function premiumOnly(): bool
    {
        return true;
    }

    public function getIcon(): string
    {
        return Icons::FILE_CHART;
    }

    public function getTitle(): string
    {
        return __('messages.pages.report.ClientProgress.title');
    }

    public function getDescription(): string
    {
        return __('messages.pages.report.ClientProgress.description');
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
            ->whereHas('avaliations', function ($query) {
                // can apply additional filters here if needed
            }, '>=', 2)
            ->with(['avaliations' => function ($query) {
                $query->orderBy('date', 'asc');
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
            Column::make('fullName')
                ->title(__('messages.models.Client.name'))
                ->format(function(Model $Model) {
                    return $Model->getName();
                }),

            Column::make('firstAvaliationDate')
                ->title(__('messages.pages.report.ClientProgress.columns.firstAvaliationDate'))
                ->format(function(Model $Model) {
                    $first = $Model->getFirstAvaliation();
                    return SysUtils::reformatDate($first->date, 'Y-m-d', __('messages.dateFormat'));
                }),

            Column::make('lastAvaliationDate')
                ->title(__('messages.pages.report.ClientProgress.columns.lastAvaliationDate'))
                ->format(function(Model $Model) {
                    $last = $Model->getLastAvaliation();
                    return SysUtils::reformatDate($last->date, 'Y-m-d', __('messages.dateFormat'));
                }),

            Column::make('weightFirstLast')
                ->title(__('messages.pages.report.ClientProgress.columns.weightFirstLast'))
                ->format(function(Model $Model) {
                    $first = $Model->getFirstAvaliation();
                    $last = $Model->getLastAvaliation();

                    return sprintf('%s -> %s', $first->getFormattedWeight(), $last->getFormattedWeight());
                }),

            Column::make('bodyFatPercFirstLast')
                ->title(__('messages.pages.report.ClientProgress.columns.bodyFatPercFirstLast'))
                ->format(function(Model $Model) {
                    $first = $Model->getFirstAvaliation();
                    $last = $Model->getLastAvaliation();

                    return sprintf('%s -> %s', $first->getFormattedBodyFat(), $last->getFormattedBodyFat());
                }),

            Column::make('muscleMassPercFirstLast')
                ->title(__('messages.pages.report.ClientProgress.columns.muscleMassPercFirstLast'))
                ->format(function(Model $Model) {
                    $first = $Model->getFirstAvaliation();
                    $last = $Model->getLastAvaliation();

                    return sprintf('%s -> %s', $first->getFormattedMuscleMassPerc(), $last->getFormattedMuscleMassPerc());
                }),

            Column::make('basalMetabolismFirstLast')
                ->title(__('messages.pages.report.ClientProgress.columns.basalMetabolismFirstLast'))
                ->format(function(Model $Model) {
                    $first = $Model->getFirstAvaliation();
                    $last = $Model->getLastAvaliation();

                    return sprintf('%s -> %s', $first->getFormattedTmb(), $last->getFormattedTmb());
                }),

            Column::make('avaliationNbr')
                ->title(__('messages.pages.report.ClientProgress.columns.avaliationNbr'))
                ->format(function(Model $Model) {
                    return '<div class="text-center">' . $Model->avaliations->count() . '</div>';
                }),
        ];
    }
}
