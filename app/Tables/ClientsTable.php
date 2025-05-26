<?php

namespace App\Tables;

use App\Models\Client;
use App\Models\User;
use App\Helpers\Permissions;
use Okipa\LaravelTable\Abstracts\AbstractTableConfiguration;
use Okipa\LaravelTable\Column;
use Okipa\LaravelTable\RowActions\DestroyRowAction;
use Okipa\LaravelTable\RowActions\EditRowAction;
use Okipa\LaravelTable\RowActions\ShowRowAction;
use Okipa\LaravelTable\Table;
use Illuminate\Database\Eloquent\Builder;

class ClientsTable extends AbstractTableConfiguration
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
            ->model(Client::class)
            ->query(function(Builder $query) {
                return $this->getQueryFilter($query);
            })
            ->rowActions(fn(Client $client) => $this->getRowActions($client));
    }

    protected function columns(): array
    {
        return [
            Column::make('first_name')
                ->title(__('messages.pages.client.table.colName'))
                ->searchable(function($query, string $searchBy) {
                    return $query->whereRaw("CONCAT(first_name, ' ', last_name) LIKE '%{$searchBy}%'");
                })
                ->format(function(Client $Client) {
                    // return first_name and last_name
                    return $Client->first_name . ' ' . $Client->last_name;
                })
                ->sortable(function($query, string $searchBy) {
                    return $query->orderByRaw("CONCAT(first_name, ' ', last_name) {$searchBy}");
                }),

            Column::make('email')
                ->title(__('messages.pages.client.table.colEmail'))
                ->searchable()
                ->sortable(),

            Column::make('phone')
                ->title(__('messages.pages.client.table.colPhone'))
                ->searchable()
                ->sortable(),
        ];
    }

    protected function results(): array
    {
        return [
            // The table results configuration.
            // As results are optional on tables, you may delete this method if you do not use it.
        ];
    }

    protected function getQueryFilter(Builder &$query): Builder
    {
        return $query->where('user_id', '=', $this->User->id ?? 0);
    }

    protected function getRowActions(Client $Client): array
    {
        return [
            $this->getViewRowAction($Client),
            $this->getEditRowAction($Client),
            $this->getDeleteRowAction($Client),
        ];
    }

    protected function getViewRowAction(Client $Client): ShowRowAction
    {
        $routeName = 'app.client.view';

        return (new ShowRowAction(
            route($routeName,
            ['codedId' => $Client->codedId])
        ))
        ->when(Permissions::checkPermission($routeName, $this->User));
    }

    protected function getEditRowAction(Client $Client): EditRowAction
    {
        $routeName = 'app.client.edit';

        return (new EditRowAction(
            route($routeName,
            ['codedId' => $Client->codedId])
        ))
        ->when(Permissions::checkPermission($routeName, $this->User));
    }

    protected function getDeleteRowAction(Client $Client): DestroyRowAction
    {
        return (new DestroyRowAction())
            ->when(Permissions::checkPermission(Permissions::ACL_CLIENT_EDIT, $this->User))
            ->confirmationQuestion(__('messages.pages.client.index.deleteConfirmation', [
                'clientName' => $Client->first_name . ' ' . $Client->last_name
            ]))
            ->feedbackMessage(__('messages.pages.client.index.deleteSuccess'));
    }
}
