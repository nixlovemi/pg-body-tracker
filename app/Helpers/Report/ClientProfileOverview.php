<?php

namespace App\Helpers\Report;

use App\Helpers\Icons;
use App\Models\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Okipa\LaravelTable\Column;
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
            Column::make('fullName')
                ->title(__('messages.models.Client.name'))
                ->format(function(Model $Model) {
                    return $Model->getName();
                }),

            Column::make('gender')
                ->title(__('messages.models.Client.fields.gender'))
                ->format(function(Model $Model) {
                    return $Model->getGenderStr();
                }),

            Column::make('age')
                ->title(__('messages.models.Client.fields.age'))
                ->format(function(Model $Model) {
                    return $Model->getAge();
                }),

            Column::make('height')
                ->title(__('messages.models.Client.fields.height'))
                ->format(function(Model $Model) {
                    return $Model->getFormattedHeight();
                }),

            Column::make('weight')
                ->title(__('messages.models.Client.fields.weight'))
                ->format(function(Model $Model) {
                    return $Model->getFormattedCurrentWeight();
                }),

            Column::make('bmi')
                ->title(__('messages.components.avaliationReport.bmi'))
                ->format(function(Model $Model) {
                    $avaliation = $Model->getLastAvaliation();
                    return $avaliation?->getFormattedBmi() ?? '';
                }),

            Column::make('lastAvaliation')
                ->title(__('messages.pages.avaliation.modalSelectClient.lastAvaliationColumn'))
                ->format(function(Model $Model) {
                    $avaliation = $Model->getLastAvaliation();
                    return $avaliation?->getFormattedDate() ?? '';
                }),
        ];
    }
}
