<?php

namespace App\Tables;

use App\Models\Client;
use Okipa\LaravelTable\Column;
use Okipa\LaravelTable\Table;
use App\Tables\Helpers\ClientsTableHelper;

class AvaliationsSelectClientTable extends ClientsTable
{
    protected function table(): Table
    {
        $table = parent::table();
        $table->rowActions(fn(Client $client) => []);

        return $table;
    }

    protected function columns(): array
    {
        $columns = parent::columns();
        return array_merge([
            Column::make('')
                ->title('')
                ->format(function(Client $Client) {
                    return '<input type="radio" name="client_cid" value="'.$Client->codedId.'" class="form-control" style="  font-size:25% !important;" />';
                })
        ], $columns, [
            ClientsTableHelper::getLastAvaliationColumn()
        ]);
    }
}
