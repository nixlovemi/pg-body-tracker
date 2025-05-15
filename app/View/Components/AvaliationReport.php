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
        return view('components.avaliation-report');
    }
}
