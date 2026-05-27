<input
    type="text"
    id="f-{{ $fieldKey }}"
    name="f-{{ $fieldKey }}"
    class="form-control"
    value="{{ $oldValue }}"
    {{ $required ? 'required' : '' }}
/>
