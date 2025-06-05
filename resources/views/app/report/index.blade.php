@inject('ReportIndexPresenters', 'App\Presenters\ReportIndex')
@inject('ReportPremium', 'App\Helpers\Feature\ReportPremium')

@php
/*
View variables:
===============
    - $PAGE_TITLE: string
*/
@endphp

@extends('layout.dashboard', [
    'PAGE_TITLE' => $PAGE_TITLE ?? ''
])

@section('DASH_BODY_CONTENT')
    <h4>{{ $PAGE_TITLE }}</h4>

    <style>
        .report-card {
            cursor: pointer;
        }
        .report-card .h5 {
            font-size: 1.15rem;
        }
        .report-card:hover {
            background-color: #f8f9fc;
        }
        .premium-badge {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            font-size: 0.7rem;
            padding: 0.4em 0.6em;
            text-transform: uppercase;
        }
        .report-card.premium-locked {
            opacity: 0.6;
            pointer-events: none;
            cursor: not-allowed;
        }
    </style>

    <div class="row">
        @foreach ($ReportIndexPresenters::getReportCardData() as $reportClass)
            @php
                $report = new $reportClass();
                $RepPremiumFeature = $ReportPremium::make($report);
            @endphp

            <div class="col-12 mb-3 col-sm-6 col-xl-3">
                <div class="report-card card border-left-primary shadow h-100 py-2 {{ !$RepPremiumFeature->validate() ? 'premium-locked' : '' }}"
                    data-url="{{ route('app.report.view', ['reportClass' => basename($reportClass)]) }}"
                >
                    @unless ($RepPremiumFeature->validate())
                        <div class="badge badge-warning premium-badge">Premium</div>
                    @endunless

                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $report->getTitle() }}
                                </div>
                            </div>
                            <div class="col-auto fa-2x">
                                {!! $report->getIcon() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <script>
        document.querySelectorAll('.report-card:not(.premium-locked)').forEach(card => {
            card.addEventListener('click', function() {
                // open new tab with the value of data-url attribute
                // window.open(this.getAttribute('data-url'), '_blank');
                window.open(this.getAttribute('data-url'), '_self');
            });
        });
    </script>
@endsection
