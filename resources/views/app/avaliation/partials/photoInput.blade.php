@php
/*
View variables:
===============
    - $AVALIATION: ?Avaliation
    - $FIELD_NAME: string
    - $INPUT_NAME: string
    - $INPUT_DEFAULT_IMAGE: string
    - $IMG_ALT: string
    - $CAN_EDIT: bool
*/
$INPUT_NAME = $INPUT_NAME ?? '';
$INPUT_DEFAULT_IMAGE = $INPUT_DEFAULT_IMAGE ?? '';
$CAN_EDIT = $CAN_EDIT ?? false;
@endphp

@if ($CAN_EDIT)
    <input name="remove_{{ $INPUT_NAME }}" type="hidden" value="0" />
    <input name="{{ $INPUT_NAME }}" class="raf-file-input" type="file" accept=".jpg,.jpeg,.png" />
@endif

<div class="raf-photo-url">
    @if ($CAN_EDIT)
        <button
            data-default-img="{{ $INPUT_DEFAULT_IMAGE }}"
            type="button"
            class="raf-remove-btn"
        >x</button>
    @endif

    <img
        class="img-fluid"
        @if ($AVALIATION?->{$FIELD_NAME})
            src="{{ route('app.avaliation.showPhoto', ['fileName' => basename($AVALIATION?->{$FIELD_NAME})]) }}?v={{ time() }}"
        @else
            src="{{ $INPUT_DEFAULT_IMAGE }}"
        @endif
        alt="{{ $IMG_ALT }}"
    />
</div>
