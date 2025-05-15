<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ChartJs extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        public string $elementId,
        public string $config, // JSON string
    ) {
        $varConfig = json_decode($this->config, true);
        $defaultConfig = [
            'type' => 'line',
            'options' => [
                'responsive' => true,
                'title' => [
                    'display' => true,
                ],
                'legend' => [
                    'display' => true,
                    'position' => 'top'
                ],
                'scales' => [
                    'yAxes' => [[
                        'offset' => false,
                        'ticks' => [
                            'padding' => 2.5,
                        ],
                        'scaleLabel' => [
                            'display' => true,
                        ]
                    ]],
                    'xAxes' => [[
                        'offset' => false,
                        'ticks' => [
                            'padding' => 2.5,
                        ],
                        'scaleLabel' => [
                            'display' => true,
                        ]
                    ]]
                ]
            ]
        ];
        $mergedConfig = array_merge($defaultConfig, $varConfig);
        $this->config = json_encode($mergedConfig);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.chart-js');
    }
}
