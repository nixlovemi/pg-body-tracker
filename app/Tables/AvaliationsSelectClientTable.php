<?php

namespace App\Tables;

use App\Helpers\SysUtils;
use App\Models\Client;
use Okipa\LaravelTable\Column;
use Okipa\LaravelTable\Table;

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
            Column::make('')
                ->title(__('messages.pages.avaliation.modalSelectClient.lastAvaliationColumn'))
                ->format(function(Client $Client) {
                    $lastAvaliation = $Client->getLastAvaliation();
                    if ($lastAvaliation) {
                        return SysUtils::reformatDate($lastAvaliation->date, 'Y-m-d', __('messages.dateFormat'));
                    }

                    return '';
                })
        ]);
    }
}
