<select
    id="f-{{ $fieldKey }}"
    name="f-{{ $fieldKey }}"
    class="form-control"
    {{ $required ? 'required' : '' }}
>
    <option value="">{{ __('messages.selectEmptyOption') }}</option>
    @foreach ($Field->getFormOptions() as $optionValue => $optionLabel)
        <option value="{{ $optionValue }}" {{ (string) $oldValue === (string) $optionValue ? 'selected' : '' }}>
            {{ __($optionLabel) }}
        </option>
    @endforeach
</select>
