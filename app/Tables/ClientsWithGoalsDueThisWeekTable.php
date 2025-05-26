<?php

namespace App\Tables;

use App\Models\Client;
use Okipa\LaravelTable\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Tables\Helpers\ClientsTableHelper;

class ClientsWithGoalsDueThisWeekTable extends ClientsTable
{
    protected function table(): Table
    {
        $table = parent::table();
        return $table;
    }

    protected function columns(): array
    {
        $columns = parent::columns();
        return array_merge($columns, [
            ClientsTableHelper::getLastAvaliationColumn(),
            ClientsTableHelper::getCurrentGoalDeadlineColumn(),
        ]);
    }

    protected function getQueryFilter(Builder &$query): Builder
    {
        $query = parent::getQueryFilter($query);

        // get clients without avaliation in the last 30 days
        $query30Days = Client::fGetClientsWithGoalsDueThisWeek();
        $query->whereIn('id', $query30Days->pluck('id'));

        return $query;
    }

    protected function getRowActions(Client $Client): array
    {
        return [
            $this->getViewRowAction($Client),
            $this->getEditRowAction($Client),
        ];
    }
}
