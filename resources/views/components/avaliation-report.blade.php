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
            'IS_PDF' => $isPdf
        ])

        <!-- Graphs -->
        @yield('AVALIATION_REPORT_GRAPHS')

        <!-- pictures -->
        @yield('AVALIATION_REPORT_PICTURES')

        <!-- observation -->
        @if (!empty($Avaliation->client_notes))
            @if ($isPdf)
                <!-- first page break -->
                <p>&nbsp;</p>
            @endif

            <div @class(['row', 'mt-3' => !$isPdf])>
                <div class="col-12">
                    <div @class(['card border-left-secondary shadow py-2'])>
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
    </div>
</div>
