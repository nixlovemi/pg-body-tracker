<textarea
    id="f-{{ $fieldKey }}"
    name="f-{{ $fieldKey }}"
    class="form-control"
    rows="4"
    {{ $required ? 'required' : '' }}
>{{ $oldValue }}</textarea>
