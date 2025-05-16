<select
    {{ (!$disabled) ?: 'disabled' }}
    class="form-control form-control-user"
    id="{{ $id }}"
    name="{{ $name }}"
>
    @foreach ($countryCodes as $country => $code)
        @php
        $selected = strpos($code, $selectedValue) !== false ? 'selected' : '';
        @endphp

        <option value="{{ $code }}" {{ $selected }}>
            {{ $code }} ({{ $country }})
        </option>
    @endforeach
</select>
