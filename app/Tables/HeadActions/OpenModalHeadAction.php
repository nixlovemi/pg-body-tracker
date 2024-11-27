<?php

namespace App\Tables\HeadActions;

use Livewire\Component;
use Okipa\LaravelTable\Abstracts\AbstractHeadAction;

class OpenModalHeadAction extends AbstractHeadAction
{
    public function __construct(
        public string $url,
        public string $label,
        public string $icon,
        public array $urlParam = [],
        public array $class = ['btn', 'btn-success']
    ) { }

    protected function class(): array
    {
        return $this->class;
    }

    protected function title(): string
    {
        return __($this->label);
    }

    protected function icon(): string
    {
        return $this->icon;
    }

    public function action(Component $livewire): void
    {
        $livewire->emit('laraveltable:link:open:modal', $this->url, json_encode($this->urlParam));
    }
}
