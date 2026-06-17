<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Helpers\AvaliationGraph\AvaliationGraphAbstract;

class AvaliationGraph extends Component
{
    private AvaliationGraphAbstract $helper;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        public int $avaliationId,
        public string $title,
        public string $helperClass,
        public bool $isPdf = false,
        public $previousAvaliations = null, // Pre-loaded previous avaliations to avoid N+1 queries
    ) {
        /** AvaliationGraphAbstract $helper */
        $this->helper = new $this->helperClass($this->avaliationId, $this->isPdf);
        $this->helper->fullHtmlTable = $this->isPdf;

        // Set pre-loaded previous avaliations if provided (optimization for PDF generation)
        if ($this->previousAvaliations !== null) {
            $this->helper->setPreviousAvaliations($this->previousAvaliations);
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.avaliation-graph', $this->helper->getData());
    }
}
