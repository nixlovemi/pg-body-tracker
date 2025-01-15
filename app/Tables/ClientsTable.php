<?php

namespace App\Tables;

use App\Models\Client;
use Okipa\LaravelTable\Abstracts\AbstractTableConfiguration;
use Okipa\LaravelTable\Column;
use Okipa\LaravelTable\Formatters\DateFormatter;
use Okipa\LaravelTable\RowActions\DestroyRowAction;
use Okipa\LaravelTable\RowActions\EditRowAction;
use Okipa\LaravelTable\Table;

class ClientsTable extends AbstractTableConfiguration
{
    protected function table(): Table
    {
        return Table::make()->model(Client::class)
            ->rowActions(fn(Client $client) => [
                new EditRowAction(route('app.client.edit', $client)),
                new DestroyRowAction(),
            ]);
    }

    protected function columns(): array
    {
        return [
            Column::make('first_name')
                ->title(__('messages.pages.client.table.colName'))
                ->searchable(function($query, string $searchBy) {
                    return $query->whereRaw("CONCAT(first_name, ' ', last_name) LIKE '%{$searchBy}%'");
                })
                ->format(function(Client $JobFile) {
                    // return first_name and last_name
                    return $JobFile->first_name . ' ' . $JobFile->last_name;
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
}
