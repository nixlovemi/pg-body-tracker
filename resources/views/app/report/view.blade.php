@inject('Icons', 'App\Helpers\Icons')
@inject('ReportExport', 'App\Helpers\Feature\ReportExport')

@php
/*
View variables:
===============
    - $PAGE_TITLE: string
    - $REPORT: App\Helpers\Report\ReportAbstract
    - $ROW_CLASS: string
    - $DISPLAY_DOWNLOADS: bool
    - $DISPLAY_LIVEWIRE: bool
*/

$reportClassName = get_class($REPORT);
$RepExportFeature = new $ReportExport();
@endphp

<div class="row {{$ROW_CLASS ?? ''}}">
    <div class="col">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h5 class="m-0 font-weight-bold text-primary">
                    {{ $PAGE_TITLE }}
                </h5>

                @if ($DISPLAY_DOWNLOADS ?? false)
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle text-gray-600" href="#" role="button" id="dropdownMenuLink"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                        >
                            {!! $Icons::DOWNLOAD !!}
                        </a>

                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">{{ __('messages.components.avaliationReport.downloadHeader') }}</div>

                            @if ($RepExportFeature->validate())
                                <a class="dropdown-item" target="_blank" href="{{ route('app.report.pdf', ['reportClass' => class_basename($reportClassName)]) }}">
                                    {!! $Icons::FILE_PDF !!}&nbsp;
                                    {{ __('messages.components.avaliationReport.downloadPdfButton') }}
                                </a>

                                <a class="dropdown-item" target="_blank" href="{{ route('app.report.csv', ['reportClass' => class_basename($reportClassName)]) }}">
                                    {!! $Icons::FILE_CSV !!}&nbsp;
                                    {{ __('messages.components.avaliationReport.downloadCsvButton') }}
                                </a>
                            @else
                                <h6 class="dropdown-item text-center">
                                    {{ __('messages.components.Features.premiumFeature') }}
                                </h6>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
            <div class="card-body">
                <p>{{ $REPORT->getDescription() }}</p>

                @if ($DISPLAY_LIVEWIRE ?? false)
                    <livewire:table
                        :config="App\Tables\ReportsTable::class"
                        :configParams="['reportClass' => $reportClassName]"
                    />
                @else
                    {!! $REPORT->generateHtml() !!}
                @endif
            </div>
        </div>
    </div>
</div>
