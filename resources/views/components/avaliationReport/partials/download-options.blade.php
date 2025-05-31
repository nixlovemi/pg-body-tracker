@inject('AvaliationSendLink', 'App\Helpers\Feature\AvaliationSendLink')

@php
/*
View variables:
===============
    - $AVALIATION: Avaliation
*/

$ASLink = new $AvaliationSendLink();
@endphp

<div class="dropdown no-arrow">
    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fa fa-download fa-fw text-gray-600"></i>
    </a>

    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink" style="">
        <div class="dropdown-header">{{ __('messages.components.avaliationReport.downloadHeader') }}</div>
        <a class="dropdown-item" target="_blank" href="{{ route('app.avaliation.viewReportPDF', ['codedId' => $AVALIATION->codedId]) }}">
            <i class="fas fa-file-pdf"></i>&nbsp;
            {{ __('messages.components.avaliationReport.downloadPdfButton') }}
        </a>

        <div class="dropdown-header">{{ __('messages.components.avaliationReport.sendLinkHeader') }}</div>
        @if ($ASLink->validate())
            <a class="dropdown-item"
                href="javascript:;" id="avaliation-send-link-whatsapp"
                data-cid="{{ urlencode($AVALIATION->codedId) }}"
            >
                {!! $Icons::WHATSAPP !!}&nbsp;
                {{ __('messages.components.avaliationReport.sendWhatsLink') }}
            </a>
            <a class="dropdown-item"
                href="javascript:;" id="avaliation-send-link-email"
                data-cid="{{ urlencode($AVALIATION->codedId) }}"
            >
                {!! $Icons::ENVELOP !!}&nbsp;
                {{ __('messages.pages.client.table.colEmail') }}
            </a>
        @else
            <h6 class="dropdown-item text-center">
                {{ __('messages.components.Features.AvaliationSendLink.shareMenuPlaceholderTitle') }}
            </h6>
        @endif
    </div>
</div>
