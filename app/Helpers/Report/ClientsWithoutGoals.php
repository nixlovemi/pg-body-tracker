<?php

namespace App\Helpers\Report;

use App\Helpers\Icons;
use App\Models\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Okipa\LaravelTable\Column;
use App\Helpers\SysUtils;

class ClientsWithoutGoals extends ReportAbstract
{
    public function getIcon(): string
    {
        return Icons::QUESTION_CIRCLE;
    }

    public function getTitle(): string
    {
        return __('messages.pages.report.ClientsWithoutGoals.title');
    }

    public function getDescription(): string
    {
        return __('messages.pages.report.ClientsWithoutGoals.description');
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
            ->doesntHave('goals')
            ->with(['avaliations' => fn($q) => $q->latest('date')])
            ->get()
            ->sortByDesc('first_name, last_name');
    }

    /**
     * Returns the columns to be displayed in the report.
     *
     * @return array[Okipa\LaravelTable\Column]
     */
    public function getColumns(): array
    {
        return [
            Column::make('full_name')
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

            Column::make('created_at')
                ->title(__('messages.pages.report.ClientsWithoutGoals.createdAt'))
                ->format(function(Model $Model) {
                    return $Model->getFormattedCreatedAt();
                }),

            Column::make('last_avaliation')
                ->title(__('messages.pages.avaliation.modalSelectClient.lastAvaliationColumn'))
                ->format(function(Model $Model) {
                    $last = $Model->getLastAvaliation();
                    if ($last) {
                        return $last->getFormattedDate();
                    }

                    return '';
                }),
        ];
    }
}
