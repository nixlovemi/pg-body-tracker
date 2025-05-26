<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Helpers\DashboardCard\CardAbstract;

class DashboardCard extends Component
{
    /**
     * Create a new component instance.
     *
     * @param $borderLeftClass [border-left-primary, border-left-secondary, border-left-success, border-left-danger, border-left-warning, border-left-info]
     * @return void
     */
    public function __construct(
        public $card,
    ) {
        if (!$this->card instanceof CardAbstract) {
            throw new \InvalidArgumentException('DashCard must extends CardAbstract');
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.dashboard-card');
    }
}
