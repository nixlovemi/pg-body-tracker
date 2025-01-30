@php
/*
Variables
    - $divId: string
    - $maxHeight: ?string
    - $maxWidth: ?string
*/

$divId = $divId ?? '';
$maxHeight = $maxHeight ?? '65vh';
$maxWidth = $maxWidth ?? '600px';
@endphp

<div
    id="{{ $divId }}"
    class="modal fade"
    tabindex="-1"
    role="dialog"
    aria-hidden="true"
    style="max-height: {{$maxHeight}};"
>
    <div
        class="modal-dialog modal-dialog-scrollable"
        style="max-width: {{ $maxWidth }}"
    >
        <div class="modal-content">
            <div class="modal-header">
                @yield('MODAL_HEADER')
            </div>
            <div class="modal-body">
                @yield('MODAL_BODY')
            </div>
        </div>
    </div>
</div>
