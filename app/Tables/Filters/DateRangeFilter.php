<?php

namespace App\Tables\Filters;

use Illuminate\Database\Eloquent\Builder;
use Okipa\LaravelTable\Abstracts\AbstractFilter;
use Illuminate\Contracts\View\View;
use Illuminate\View\ComponentAttributeBag;
use App\Helpers\SysUtils;

class DateRangeFilter extends AbstractFilter
{
    function __construct(
        public string $modelFieldName
    ) { }

    protected function identifier(): string
    {
        // The unique identifier that is required to retrieve the filter.
        return 'date-range-filter';
    }

    protected function class(): array
    {
        return [
            // The CSS class that will be merged to the existent ones on the filter select.
            // As class are optional on filters, you may delete this method if you don't declare any specific class.
            // Note: you can use conditional class merging as specified here: https://laravel.com/docs/blade#conditionally-merge-classes
            ...parent::class(),
        ];
    }

    protected function attributes(): array
    {
        return [
            // The HTML attributes that will be merged to the existent ones on the filter select.
            // As attributes are optional on filters, you may delete this method if you do declare any specific attributes.
            ...parent::attributes(),
            'placeholder' => '',
        ];
    }

    protected function label(): string
    {
        // The label that will appear in the filter select.
        return __('messages.okipaDateRangeLabel');
    }

    /** Doesn't use */
    protected function multiple(): bool
    {
        // Whether the filter select should allow multiple option to be selected.
        return false;
    }

    /** Doesn't use */
    protected function options(): array
    {
        // The options that will be available in the filter select.
        return [];
    }

    /**
     * https://github.com/livewire/livewire/issues/206#issuecomment-657046279
     */
    public function filter(Builder $query, mixed $selected): void
    {
        // The filtering treatment that will be executed on option selection.
        // The $selected attribute will provide an array in multiple mode and a value in single mode.
        foreach ($selected as $inputType => $date) {
            $yearSymbol = ($inputType === 'start') ? '-': '+';
            $whereSymbol = ($inputType === 'start') ? '>=': '<=';

            if (empty($date)) {
                $query->where($this->modelFieldName, $whereSymbol, date('Y-m-d', strtotime($yearSymbol . '100 years')));
                continue;
            }

            if (strlen($date) !== 10) {
                continue;
            }

            $date = SysUtils::reformatDate($date, __('messages.dateFormat'), 'Y-m-d');
            if (false === \DateTime::createFromFormat('Y-m-d', $date)) {
                continue;
            }

            $query->where($this->modelFieldName, $whereSymbol, $date);
        }
    }

    public function render(): View
    {
        return view()->file(app_path('Tables/Filters/views/dateRangeFilterView.blade.php'), [
            'filter' => $this,
            'class' => $this->class(),
            'attributes' => (new ComponentAttributeBag($this->attributes())),
            'label' => $this->label(),
            'options' => $this->options(),
            'multiple' => $this->multiple(),
        ]);
    }
}
