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
use App\Helpers\Report\ReportAbstract;

class ReportsTable extends AbstractTableConfiguration
{
    public string $reportClass;
    public ReportAbstract $report;

    protected function table(): Table
    {
        $this->init();

        return Table::make()
            ->numberOfRowsPerPageOptions([50])
            ->model(get_class($this->report->getModel()))
            ->query(function(Builder $query) {
                $this->report->applyFilter($query);
                return $query;
            });
    }

    private function init(): void
    {
        $this->report = new $this->reportClass();
    }

    protected function columns(): array
    {
        return $this->report->getColumns();
    }

    protected function results(): array
    {
        return [
            // The table results configuration.
            // As results are optional on tables, you may delete this method if you do not use it.
        ];
    }
}
