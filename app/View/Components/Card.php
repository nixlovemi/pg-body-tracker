<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Card extends Component
{
    public int $cardId;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        public string $title,
        public bool $noMarginBottom = false,
    ) {
        $this->getCardId();
    }

    private function getCardId(): int
    {
        return $this->cardId = round(microtime(true) * 1000) . rand(1, 99);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.card');
    }
}
