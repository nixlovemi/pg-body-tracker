@inject('Icons', 'App\Helpers\Icons')
@inject('Constants', 'App\Helpers\Constants')

<div class="avaliation-report-body card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">
            {{ __('messages.components.avaliationReport.title', [
                'clientName' => $Avaliation->client->getName()
            ]) }}
        </h6>

        @unless ($isPdf)
            @include('components.avaliationReport.partials.download-options', [
                'AVALIATION' => $Avaliation
            ])
        @endunless
    </div>
    <div class="card-body">
        <!-- General Info -->
        @yield('AVALIATION_REPORT_GENERAL_INFO')

        <!-- Info Cards -->
        @include('components.avaliationReport.partials.info-cards', [
            'AVALIATION' => $Avaliation,
            'IS_PDF' => $isPdf,
            'infoCardsData' => $infoCardsData ?? null,
        ])

        <!-- Graphs -->
        @if ($includeGraphs ?? true)
            @yield('AVALIATION_REPORT_GRAPHS')
        @endif

        <!-- pictures -->
        @if ($includePictures ?? true)
            @yield('AVALIATION_REPORT_PICTURES')
        @endif

        <!-- observation -->
        @yield('AVALIATION_REPORT_CLIENT_NOTES')
    </div>
</div>
