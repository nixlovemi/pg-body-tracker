<?php

namespace App\Helpers\AvaliationGraph;

use App\Models\Avaliation;
use App\Helpers\Constants;

abstract class AvaliationGraphAbstract
{
    public bool $fullHtmlTable = false;

    private array $tableData = [
        'head' => [],
        'body' => [],
    ];
    protected string $defaultColor = Constants::RANK_COLOR_DEFAULT;
    private $cachedPreviousAvaliations = null; // Cache for pre-loaded previous avaliations

    public function __construct(
        protected int $avaliationId,
        protected bool $isForPdf = false
    ) { }

    abstract protected function getAvaliation(): Avaliation;
    abstract protected function getConfig(): array;
    abstract protected function getClassName(): string;

    /**
     * Set pre-loaded previous avaliations to avoid N+1 queries during PDF generation.
     *
     * @param \Illuminate\Database\Eloquent\Collection $previousAvaliations
     * @return void
     */
    public final function setPreviousAvaliations($previousAvaliations): void
    {
        $this->cachedPreviousAvaliations = $previousAvaliations;
    }

    protected final function getUID(): string
    {
        return $this->getClassName() . '-' . $this->getAvaliation()->codedId;
    }

    protected final function addHeadItem(string $string): void
    {
        $this->tableData['head'][] = $string;
    }

    protected final function addBodyItem(string ...$string): void
    {
        $this->tableData['body'][] = $string;
    }

    protected final function getPreviousAvaliations(int $limit)
    {
        // If pre-loaded avaliations are available (from PDF generation), use them
        if ($this->cachedPreviousAvaliations !== null) {
            return $this->cachedPreviousAvaliations->take($limit);
        }

        // Otherwise, fetch from database (used in web reports, etc)
        $Avaliation = $this->getAvaliation();

        return Avaliation::where('date', '<', $Avaliation->date)
            ->where('client_id', $Avaliation->client_id)
            ->where('id', '!=', $Avaliation->id)
            ->orderByDesc('date')
            ->limit($limit)
            ->get();
    }

    protected final function getTableRowLabel(string $label, string $color): string
    {
        if ($this->isForPdf) {
            $str = '<div style="position:relative; top-8px; padding-bottom:2px; border-bottom:solid 6px; ';
            $str .= ' border-bottom-color:%s; border-bottom-color:%s;">%s</div>';
        } else {
            $str = '<a href="javascript:;" class="btn btn-primary btn-circle btn-sm" style="margin-right:4px; ';
            $str .= ' width:18px; height:18px; background-color:%s; border-color:%s;">&nbsp;</a>%s';
        }

        return sprintf(
            $str,
            $color,
            $color,
            $label
        );
    }

    private function getDataTableHtml(): string
    {
        $html = '<table class="table table-borderless" style="font-size:80%;">';
        $html .= '<thead class="font-weight-bold"><tr class="table-light border-top border-bottom">';
        foreach ($this->tableData['head'] as $item) {
            $html .= '<th class="align-middle" scope="col"><span class="ms-2">' . $item . '</span></th>';
        }
        $html .= '</tr></thead><tbody>';
        foreach ($this->tableData['body'] as $row) {
            $html .= '<tr class="border-bottom">';
            foreach ($row as $item) {
                $html .= '<td class="align-middle" scope="row">' . $item . '</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';

        $tableClass = $this->fullHtmlTable ? 'col-12' : 'col-8 offset-2';
        return '
            <div class="row mt-3">
                <div class="'.$tableClass.' table-responsive">
                    '.$html.'
                </div>
            </div>';
    }

    public final function getData(): array
    {
        try {
            $config = $this->getConfig();
        } catch (\Throwable $e) {
            \App\Helpers\LocalLogger::log(self::class . ' error', ['exception' => $e->getMessage()]);
            $config = [];
        }

        return [
            'config' => json_encode($config),
            'UID' => $this->getUID(),
            'defaultColor' => $this->defaultColor,
            'dataTableHtml' => $this->getDataTableHtml(),
        ];
    }
}
