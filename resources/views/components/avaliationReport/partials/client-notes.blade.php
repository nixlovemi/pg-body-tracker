@inject('ARPresenter', 'App\Presenters\AvaliationReportPresenter')

@php
/*
View variables:
===============
    - $AVALIATION: Avaliation
    - $HAS_PAGE_BREAK: bool
    - $DIV_ROW_CLASSES: string
*/
@endphp

@if (!empty($Avaliation->client_notes))
    @if ($HAS_PAGE_BREAK)
        <div class="page-break"></div>
        <br />
    @endif

    <div class="{{ $DIV_ROW_CLASSES }}">
        <div class="col-12">
            <div class="card border-left-secondary shadow py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-sm font-weight-bold text-secondary text-uppercase mb-1">
                                {{ __('messages.pages.avaliation.modalAddAvaliation.pageFiveTitle') }}
                            </div>

                            <p>
                                {!! nl2br($Avaliation->client_notes) !!}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
