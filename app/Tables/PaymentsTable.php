<?php

namespace App\Tables;

use App\Models\UserPlans;
use Okipa\LaravelTable\Abstracts\AbstractTableConfiguration;
use Okipa\LaravelTable\Column;
use App\Tables\RowActions\OpenModalRowAction;
use Okipa\LaravelTable\Table;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use App\Helpers\Icons;

class PaymentsTable extends AbstractTableConfiguration
{
    public int $userId;
    private User $User;

    private function init(): void
    {
        $this->User = User::find($this->userId);
    }

    protected function table(): Table
    {
        $this->init();

        return Table::make()
            ->model(UserPlans::class)
            ->query(function(Builder $query) {
                return $query->where('user_id', '=', $this->User->id ?? 0)
                    ->orderBy('start_date', 'desc')
                    ->orderBy('id', 'desc');
            })
            ->rowActions(fn(UserPlans $UserPlans) => [
                (new OpenModalRowAction(
                    __('messages.tableActionEdit'),
                    route('app.subscription.details', ['codedId' => $UserPlans->codedId, 'json' => '1']),
                    Icons::EYE
                )),
            ]);
    }

    protected function columns(): array
    {
        return [
            Column::make('ID')
                ->title(__('messages.pages.premium.paymentHistory.colID'))
                ->format(function(UserPlans $UserPlans) {
                    $id = $UserPlans->logs?->last() ?
                        $UserPlans->logs?->last()?->getColIdString() :
                        '-';

                    return $id;
                }),

            Column::make('plan_type')
                ->title(__('messages.models.UserPlans.fields.plan_type'))
                ->format(function(UserPlans $UserPlans) {
                    return $UserPlans->getPlanTypeLabel();
                }),

            Column::make('status')
                ->title(__('messages.models.UserPlans.fields.status'))
                ->format(function(UserPlans $UserPlans) {
                    return $UserPlans->getStatuslabel();
                }),

            Column::make('start_date')
                ->title(__('messages.models.UserPlans.fields.start_date'))
                ->format(function(UserPlans $UserPlans) {
                    return $UserPlans->getFormattedStartDate();
                }),

            Column::make('end_date')
                ->title(__('messages.models.UserPlans.fields.end_date'))
                ->format(function(UserPlans $UserPlans) {
                    return $UserPlans->getFormattedEndDate();
                }),

            Column::make('last_update')
                ->title(__('messages.pages.premium.paymentHistory.colLastUpdate'))
                ->format(function(UserPlans $UserPlans) {
                    return $UserPlans->logs?->last()?->created_at ?
                        $UserPlans->logs?->last()?->getFormattedCreatedAt(true) :
                        '-';
                }),
        ];
    }
}
