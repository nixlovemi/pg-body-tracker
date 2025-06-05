<?php

namespace App\Helpers\Report;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

abstract class ReportAbstract
{
    abstract public function getIcon(): string;
    abstract public function getTitle(): string;
    abstract public function getDescription(): string;

    abstract public function getModel(): Model;
    abstract public function applyFilter(Builder &$query);
    /**
     * Returns the columns to be displayed in the report.
     *
     * @return array[Okipa\LaravelTable\Column]
     */
    abstract public function getColumns(): array;

    final public function generateHtml(): string
    {
        $columns = $this->getColumns();

        $html = '<table class="table table-borderless">';
        $html .= '<thead><tr>';
        foreach ($columns as $column) {
            $html .= '<th>' . $column->getTitle() . '</th>';
        }
        $html .= '</tr></thead>';
        $html .= '<tbody>';
        $model = $this->getModel();
        $query = $model::query();
        $this->applyFilter($query);
        $results = $query->get();
        foreach ($results as $result) {
            $html .= '<tr>';
            foreach ($columns as $column) {
                $html .= '<td>' . $column->getValue($result, []) . '</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</tbody>';
        $html .= '</table>';

        return $html;
    }

    final public function generateCsv(): string
    {
        $columns = $this->getColumns();
        $separator = ',';
        $csv = "\xEF\xBB\xBF"; // BOM for UTF-8

        // Add header row
        $header = [];
        foreach ($columns as $column) {
            $header[] = $column->getTitle();
        }
        $csv .= implode($separator, $header) . "\n";

        // Add data rows
        $model = $this->getModel();
        $query = $model::query();
        $this->applyFilter($query);
        $results = $query->get();
        foreach ($results as $result) {
            $row = [];
            foreach ($columns as $column) {
                // remove any html tags so <div class=""text-center"">103</div> can display as "103"
                $columnValue = strip_tags($column->getValue($result, []));
                // escape double quotes for CSV
                $columnValue = str_replace('"', '""', $columnValue);
                // add quotes around the value to handle commas and new lines
                $row[] = '"' . trim($columnValue) . '"';
            }
            $csv .= implode($separator, $row) . "\r\n";
        }

        return $csv;
    }

    public function premiumOnly(): bool
    {
        return false;
    }
}
