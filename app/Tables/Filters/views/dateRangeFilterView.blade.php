<style>
    ul.okipa-filter-date-range {
        display: flex;
        justify-content: center;
        align-items: center;
        list-style: none;
        padding: 0;
        position: relative;
        top: 12px;
    }
    ul.okipa-filter-date-range li {
        margin: 0 5px;
    }
</style>

<ul class="okipa-filter-date-range">
    <li>{{ $label }}</li>
    <li>
        <input type="text" class="form-control form-control-user jq-datepicker"
            id="okf-{{ $filter->identifier }}-start" name="okf-{{ $filter->identifier }}-start"
            wire:model="selectedFilters.{{ $filter->identifier }}.start" {{ $attributes->class(['form-select', ...$class])->except(['placeholder']) }}
            maxlength="10" onchange="this.dispatchEvent(new InputEvent('input'))"
            placeholder="{{ __('messages.okipaDateRangePlaceholderStart') }}"
        />
    </li>

    <li> - </li>

    <li>
        <input type="text" class="form-control form-control-user jq-datepicker"
            id="okf-{{ $filter->identifier }}-end" name="okf-{{ $filter->identifier }}-end"
            wire:model="selectedFilters.{{ $filter->identifier }}.end" {{ $attributes->class(['form-select', ...$class])->except(['placeholder']) }}
            maxlength="10" onchange="this.dispatchEvent(new InputEvent('input'))"
            placeholder="{{ __('messages.okipaDateRangePlaceholderEnd') }}"
        />
    </li>
</ul>
