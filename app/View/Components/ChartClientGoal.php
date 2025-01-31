<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\Client;
use App\Helpers\SysUtils;

class ChartClientGoal extends Component
{
    public ?Client $Client;
    public int $chartId;
    public array $arrData;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        public int $clientId
    ) {
        $this->Client = Client::find($this->clientId);
        $this->getChartId();
        $this->prepareDataArray();
    }

    private function getChartId(): int
    {
        return $this->chartId = round(microtime(true) * 1000) . rand(1, 99);
    }

    private function prepareDataArray()
    {
        $currentGoal = $this->Client->getCurrentGoal();
        if (!$currentGoal) {
            return;
        }

        // init structure
        $this->arrData = [
            'labels' => [],
            'datasets' => [
                [
                    'label' => __('messages.models.Client.fields.weight') . '(kg)',
                    'data' => [],
                    'borderColor' => 'blue',
                    'backgroundColor' => 'rgba(0, 0, 255, 0.2)',
                    'fill' => false,
                    'tension' => 0.1
                ]
            ],
        ];

        // add initial weight data
        $this->arrData['labels'][] = SysUtils::timezoneDate($currentGoal->created_at, strtolower(__('messages.dateFormat')));
        $this->arrData['datasets'][0]['data'][] = $currentGoal->initial_weight_kg;

        // add avaliations data
        $avaliationsBtwStartAndDeadline = $this->Client->avaliations
            ->where('date', '>', $currentGoal->created_at)
            ->where('date', '<=', $currentGoal->deadline)
            ->sortBy('date')
            ->slice(-8);
        foreach ($avaliationsBtwStartAndDeadline as $avaliation) {
            $this->arrData['labels'][] = SysUtils::timezoneDate($avaliation->date, strtolower(__('messages.dateFormat')));
            $this->arrData['datasets'][0]['data'][] = $avaliation->weight_kg;
        }

        // add goal data
        $this->arrData['datasets'][] = [
            'label' => __('messages.models.Goal.fields.target_weight') . '(kg)',
            'data' => [],
            'borderColor' => 'green',
            'borderDash' => [5, 5],
            'fill' => false,
            'tension' => 0.1
        ];

        // get last index from arrData datasets
        $lastIndex = count($this->arrData['datasets']) - 1;
        for ($i = 0; $i <= $this->Client->avaliations->count(); $i++) {
            $this->arrData['datasets'][$lastIndex]['data'][] = $currentGoal->target_weight_kg;
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.chart-client-goal');
    }
}
