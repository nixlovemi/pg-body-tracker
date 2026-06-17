<?php

namespace App\View\Components;

use App\Models\Avaliation;
use Illuminate\View\Component;

class AvaliationReport extends Component
{
    public Avaliation $Avaliation;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        public int $avaliationId,
        public bool $isPdf = false,
        public $previousAvaliations = null, // Pre-loaded for PDF optimization
        public $infoCardsData = null, // Pre-calculated card data for PDF optimization
        public bool $includeGraphs = true,
        public bool $includePictures = true,
    ) {
        $this->Avaliation = Avaliation::find($this->avaliationId);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        if ($this->isPdf) {
            return view('components.avaliationReport.avaliation-report-pdf');
        }

        return view('components.avaliationReport.avaliation-report-web');
    }
}
