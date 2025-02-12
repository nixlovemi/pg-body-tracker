<?php

namespace App\View\Components;

use Illuminate\View\Component;

class InfoIconModal extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        public string $title,
        public string $message,
    ) { }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return <<<'blade'
            <a href="javascript:;" class="show-info"
            data-title="{{$title}}"
            data-content="{{$message}}">
                <i class="fas fa-info-circle"></i>
            </a>
        blade;
    }
}
