@php
/*
View variables:
===============
    - $MODEL: ?Model
    - $FIELD_NAME: string
    - $INPUT_NAME: string
    - $INPUT_DEFAULT_IMAGE: string
    - $IMG_ALT: string
    - $CUSTOM_CLASS: ?string
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
        class="img-fluid {{$CUSTOM_CLASS ?? ''}}"
        src="{{ $MODEL?->getPhotoBase64($FIELD_NAME, $INPUT_DEFAULT_IMAGE) }}"
        alt="{{ $IMG_ALT }}"
    />
</div>
