@inject('Icons', 'App\Helpers\Icons')
@inject('Constants', 'App\Helpers\Constants')

@php
// TODO: maybe create a card component or change the other to hide the accordeon thing
@endphp

<style>
    .avaliation-report-body label {
        font-weight: bold;
    }
    .avaliation-report-body .progress {
        height: 45px;
    }
    .avaliation-report-body .progress-bar {
        width: 12.5%;
    }
    .avaliation-report-body #arrow-progress {
        font-size: 150%;
    }
    .avaliation-report-body .arrow-progress,
    .avaliation-report-body .arrow-progress .progress-bar {
        background-color: transparent;
        text-align: center;
        color: #5a5c69;
    }
    .avaliation-report-body .arrow-progress {
        margin-top: -10px;
    }
    .is-pdf-progress-div {
        width: 12.5%;
        height: 50px;
        color: white;
        text-align: center;
        float: left;
    }
    .is-pdf-card-graph {
        height: 671px;
        overflow-y: hidden;
    }
</style>

<div class="avaliation-report-body card shadow mb-4">
    <!--
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            {{ __('messages.components.avaliationReport.title', [
                'clientName' => $Avaliation->client->getName()
            ]) }}
        </h6>
    </div>
    -->
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">
            {{ __('messages.components.avaliationReport.title', [
                'clientName' => $Avaliation->client->getName()
            ]) }}
        </h6>

        @if (!$isPdf)
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-download fa-fw text-gray-600"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink" style="">
                    <a class="dropdown-item" target="_blank" href="{{ route('app.avaliation.viewReportPDF', ['codedId' => $Avaliation->codedId]) }}">
                        <i class="fas fa-file-pdf"></i>&nbsp;
                        {{ __('messages.components.avaliationReport.downloadPdfButton') }}
                    </a>
                </div>
            </div>
        @endif
    </div>
    <div class="card-body">
        <div class="row">
            <div @class(['col-12 mb-3 col-lg-4 mb-lg-0' => !$isPdf, 'col-5 mb-3' => $isPdf])>
                <div @class(['card border-left-secondary shadow py-2', 'h-100' => !$isPdf])>
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-sm font-weight-bold text-secondary text-uppercase mb-1">
                                    {{ __('messages.components.avaliationReport.cardInfo') }}
                                </div>
                                <div class="h6 mb-1 text-gray-800">
                                    <span class="font-weight-bold">{{ __('messages.models.Client.fields.first_name') }}:</span>
                                    {{ $Avaliation->client->getName() }}
                                </div>
                                <div class="h6 mb-1 text-gray-800">
                                    <span class="font-weight-bold">{{ __('messages.models.Client.fields.gender') }}:</span>
                                    {{ $Avaliation->client->getGenderStr() }}
                                </div>
                                <div class="h6 mb-1 text-gray-800">
                                    <span class="font-weight-bold">{{ __('messages.models.Client.fields.age') }}:</span>
                                    {{ $Avaliation->age }}
                                </div>
                                <div class="h6 mb-1 text-gray-800">
                                    <span class="font-weight-bold">{{ __('messages.models.Client.fields.height') }}:</span>
                                    {{ $Avaliation->height_cm }} cm
                                </div>
                                <div class="h6 mb-1 text-gray-800">
                                    <span class="font-weight-bold">{{ __('messages.components.avaliationReport.method') }}:</span>
                                    {{ $Avaliation->getFormattedCalculatePercFatBy() }}
                                </div>
                                <div class="h6 mb-1 text-gray-800">
                                    <span class="font-weight-bold">{{ __('messages.models.Avaliation.fields.date') }}:</span>
                                    {{ $Avaliation->getFormattedDate() }}
                                </div>
                            </div>
                        </div>

                        @if ($isPdf)
                            <!-- matching size with Progress card -->
                            &nbsp;<br />
                        @endif
                    </div>
                </div>
            </div>

            <div @class(['col-12 mb-3 col-lg-8 mb-lg-0' => !$isPdf, 'col-7 mb-3' => $isPdf])>
                <div @class(['card border-left-secondary shadow py-2', 'h-100' => !$isPdf])>
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-sm font-weight-bold text-secondary text-uppercase mb-1">
                                    {{ __('messages.pages.goal.modalAddGoal.labelProgress') }}
                                    <br />
                                    <small><i>
                                        {{ __('messages.components.avaliationReport.progressSub', [
                                            'bmi' => __('messages.components.avaliationReport.bmi'),
                                            'bodyFat' => __('messages.components.avaliationReport.bodyFat'),
                                            'whr' => __('messages.components.avaliationReport.WaistToHipRatio'),
                                            'visceralFat' => __('messages.models.Avaliation.fields.visceral_fat_kg'),
                                        ]) }}
                                    </i></small>
                                </div>

                                <div>
                                    <div class="progress mt-5">
                                        @php
                                            $arrBars = $Constants::getRankBarInfo();
                                            $bci = $Avaliation->getBciInfo();
                                        @endphp

                                        @foreach ($arrBars as $bar)
                                            @if ($isPdf)
                                                <div class="is-pdf-progress-div" style="background-color:{{ $bar['color'] }};">
                                                    <span style="position:relative; top:21.5px;">{{ $bar['label'] }}</span>
                                                </div>
                                            @else
                                                <div style="background-color:{{ $bar['color'] }};" class="progress-bar" role="progressbar" aria-valuenow="12.5" aria-valuemin="0" aria-valuemax="100">
                                                    <span class="d-inline d-md-none">{{ $bar['labelMin'] }}</span>
                                                    <span class="d-none d-md-inline">{{ $bar['label'] }}</span>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>

                                    <div class="progress arrow-progress">
                                        @foreach ($arrBars as $bar)
                                            @if ($isPdf)
                                                <div class="is-pdf-progress-div">
                                                    @if ($loop->index+1 == ($bci['rank'] ?? null))
                                                        <span id="arrow-progress" style="color:#5a5c69; font-size:150%; position:relative; top:16px;">{!! $Icons::ARROW_UP !!}</span>
                                                    @endif
                                                </div>
                                            @else
                                                <div class="progress-bar" role="progressbar" aria-valuenow="12.5" aria-valuemin="0" aria-valuemax="100">
                                                    @if ($loop->index+1 == ($bci['rank'] ?? null))
                                                        <span id="arrow-progress" style="font-size:150%">{!! $Icons::ARROW_UP !!}</span>
                                                    @endif
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-0 mt-lg-3">
            @php
            $arrInfoLoop = [
                [
                    'method' => 'getWeightInfo',
                    'title' => __('messages.models.Client.fields.weight'),
                    'showReference' => true
                ],
                [
                    'method' => 'getSkeletalMuscleInfo',
                    'title' => __('messages.components.avaliationReport.skeletalMuscle'),
                    'showReference' => true
                ],
                [
                    'method' => 'getBodyWaterInfo',
                    'title' => __('messages.components.avaliationReport.bodyWater'),
                    'showReference' => true
                ],
                [
                    'method' => 'getBoneMassInfo',
                    'title' => __('messages.models.Avaliation.fields.bone_mass_kg'),
                    'showReference' => true
                ],
                [
                    'method' => 'getBodyAgeInfo',
                    'title' => __('messages.models.Avaliation.fields.body_age'),
                    'showReference' => false
                ],
                [
                    'method' => 'getFFMIInfo',
                    'title' => __('messages.components.avaliationReport.FFMI'),
                    'showReference' => true
                ],
                [
                    'method' => 'getBmiInfo',
                    'title' => __('messages.components.avaliationReport.bmi'),
                    'showReference' => true
                ],
                [
                    'method' => 'getBodyFatInfo',
                    'title' => __('messages.components.avaliationReport.bodyFat'),
                    'showReference' => true
                ],
                [
                    'method' => 'getBAInfo',
                    'title' => __('messages.components.avaliationReport.BAI'),
                    'showReference' => true
                ],
                [
                    'method' => 'getVisceralFatInfo',
                    'title' => __('messages.models.Avaliation.fields.visceral_fat_kg'),
                    'showReference' => true
                ],
                [
                    'method' => 'getBasalMetabolismInfo',
                    'title' => __('messages.models.Avaliation.fields.basal_metabolism'),
                    'showReference' => true
                ],
                [
                    'method' => 'getWaistToHipRatioInfo',
                    'title' => __('messages.components.avaliationReport.WaistToHipRatio'),
                    'showReference' => true
                ]
            ];
            @endphp

            @foreach ($arrInfoLoop as $item)
                @php
                $info = $Avaliation->{$item['method']}();
                @endphp

                <div @class(['mb-3', 'col-12 col-lg-4' => !$isPdf, 'col-6' => $isPdf])>
                    @include('components.avaliation-report-info-card', [
                        'TITLE' => $item['title'],
                        'COLOR' => $info[$Constants::FI_RANK_COLOR] ?? '',
                        'RESULT' => $info[$Constants::FI_FIELD_LABEL] ?? '',
                        'DIAGNOSIS' => $info[$Constants::FI_RANK_LABEL] ?? '',
                        'REFERENCE' => $item['showReference'] ? ($info[$Constants::FI_IDEAL_LABEL] ?? '') : null,
                        'IS_PDF' => $isPdf,
                    ])
                </div>
            @endforeach
        </div>

        @if ($isPdf)
            <!-- first page break -->
            <p>&nbsp;</p>
        @endif

        @php
        $arrGraph = [
            ['title' => __('messages.models.Client.fields.weight'), 'helperClass' => 'App\Helpers\AvaliationGraph\AvaliationWeightGraphHelper'],
            ['title' => __('messages.components.avaliationFatLeanMassGraph.title', [
                'fatMass' => __('messages.models.Avaliation.labelFatMass'),
                'leanMass' => __('messages.models.Avaliation.labelLeanMass'),
            ]), 'helperClass' => 'App\Helpers\AvaliationGraph\AvaliationFatLeanMassGraphHelper'],
            ['title' => __('messages.components.AvaliationBodyCompositionGraph.title', []), 'helperClass' => 'App\Helpers\AvaliationGraph\AvaliationBodyCompositionGraphHelper'],
            ['title' => __('messages.components.AvaliationMuscleFatPercGraph.title', [
                'fatMass' => __('messages.models.Avaliation.labelFatMass'),
                'skeletalMuscle' => __('messages.components.avaliationReport.skeletalMuscle'),
            ]), 'helperClass' => 'App\Helpers\AvaliationGraph\AvaliationMuscleFatPercGraphHelper'],
            ['title' => __('messages.components.AvaliationFatBySegmentGraph.title', []), 'helperClass' => 'App\Helpers\AvaliationGraph\AvaliationFatBySegmentGraphHelper'],
            ['title' => __('messages.components.AvaliationLeanBySegmentGraph.title', []), 'helperClass' => 'App\Helpers\AvaliationGraph\AvaliationLeanBySegmentGraphHelper'],
            ['title' => __('messages.components.avaliationReport.bodyWater', []), 'helperClass' => 'App\Helpers\AvaliationGraph\AvaliationBodyWaterGraphHelper'],
            ['title' => __('messages.models.Avaliation.fields.body_age', []), 'helperClass' => 'App\Helpers\AvaliationGraph\AvaliationBodyAgeGraphHelper'],
            ['title' => __('messages.components.AvaliationUpperLimbsGraph.title'), 'helperClass' => 'App\Helpers\AvaliationGraph\AvaliationUpperLimbsGraphHelper'],
            ['title' => __('messages.components.AvaliationLowerLimbsGraph.title'), 'helperClass' => 'App\Helpers\AvaliationGraph\AvaliationLowerLimbsGraphHelper']
        ];
        @endphp

        <div class="row">
            @foreach ($arrGraph as $graph)
                <div class="col-12 col-lg-6 mb-3">
                    <div @class(['card border-left-secondary shadow py-2', 'h-100' => !$isPdf, 'is-pdf-card-graph' => $isPdf])>
                        <div class="card-body">
                            <x-avaliation-graph
                                :avaliationId="$Avaliation->id"
                                :isPdf="$isPdf"
                                title="{{ $graph['title'] }}"
                                helperClass="{{  $graph['helperClass'] }}"
                            />
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
