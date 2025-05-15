@php
/*
View variables:
===============
    - $TITLE: string
    - $COLOR: string
    - $RESULT: string
    - $DIAGNOSIS: string
    - $REFERENCE: string
    - $IS_PDF: boolean
*/

$IS_PDF = isset($IS_PDF) ? $IS_PDF : false;
@endphp

<div @class(['card border-left-secondary shadow py-2', 'h-100' => !$IS_PDF]) style="border-left-color:{{ $COLOR }} !important;">
    <div class="card-body">
        <div class="row no-gutters align-items-center">
            <div class="col mr-2">
                <div class="text-sm font-weight-bold text-secondary text-uppercase mb-1" style="color:{{ $COLOR }} !important;">
                    {{ $TITLE ?? '' }}
                </div>

                <div class="h6 mb-1 text-gray-800">
                    <span class="font-weight-bold">{{ __('messages.components.avaliationReport.result') }}:</span>
                    {!! $RESULT ?? '' !!}
                </div>

                <div class="h6 mb-1 text-gray-800">
                    <span class="font-weight-bold">{{ __('messages.components.avaliationReport.diagnosis') }}:</span>
                    {{ $DIAGNOSIS ?? '' }}
                </div>

                @if ((isset($REFERENCE) && !empty($REFERENCE)) || ($IS_PDF))
                    <div class="h6 mb-1 text-gray-800 font-italic">
                        @if ($IS_PDF && (!isset($REFERENCE) || empty($REFERENCE)))
                            &nbsp;
                        @else
                            <span class="font-weight-bold">{{ __('messages.components.avaliationReport.reference') }}:</span>
                            <i>
                                {{ $REFERENCE ?? '' }}
                            </i>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
