@inject('Icons', 'App\Helpers\Icons')
@inject('Constants', 'App\Helpers\Constants')

<style>
    .is-pdf-card-graph {
        height: 1000px;
        overflow-y: hidden;
    }
    .is-pdf-card-graph-first {
        height: 800px;
        overflow-y: hidden;
    }
    .is-pdf-picture-col {
        height: 500px;
    }
    #uinfo-logo {
        max-width: 200px;
    }
</style>

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

        <!-- Graphs -->
        <div class="row">
            @foreach ($arrGraph as $graph)
                <div class="col-12 col-lg-6 mb-3">
                    <div @class([
                        'card border-left-secondary shadow py-2',
                        'h-100' => !$isPdf,
                        'is-pdf-card-graph-first' => $isPdf && $loop->index === 0,
                        'is-pdf-card-graph' => $isPdf && $loop->index !== 0,
                    ])>
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

                @if ($isPdf)
                    <div class="page-break"></div>
                    <br />
                @endif
            @endforeach
        </div>

        <!-- pictures -->
        <div class="row">
            <div class="col-12">
                <div @class(['card border-left-secondary shadow py-2'])>
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-sm font-weight-bold text-secondary text-uppercase mb-1">
                                    {{ __('messages.pages.avaliation.modalAddAvaliation.pageSixTitle') }}
                                </div>

                                @php
                                    $arrPhotoLoop = [
                                        [
                                            'fieldName' => 'photo_front_url',
                                            'inputName' => 'f-photo_front_url',
                                            'defaultImg' => '/images/photo_front.jpg',
                                            'imgAlt' => __('messages.models.Avaliation.fields.photo_front_url'),
                                        ],
                                        [
                                            'fieldName' => 'photo_right_url',
                                            'inputName' => 'f-photo_right_url',
                                            'defaultImg' => '/images/photo_right.jpg',
                                            'imgAlt' => __('messages.models.Avaliation.fields.photo_right_url'),
                                        ],
                                        [
                                            'fieldName' => 'photo_rear_url',
                                            'inputName' => 'f-photo_rear_url',
                                            'defaultImg' => '/images/photo_rear.jpg',
                                            'imgAlt' => __('messages.models.Avaliation.fields.photo_rear_url'),
                                        ],
                                        [
                                            'fieldName' => 'photo_left_url',
                                            'inputName' => 'f-photo_left_url',
                                            'defaultImg' => '/images/photo_left.jpg',
                                            'imgAlt' => __('messages.models.Avaliation.fields.photo_left_url'),
                                        ]
                                    ];
                                @endphp

                                <div class="row">
                                    @foreach ($arrPhotoLoop as $item)
                                        <div @class(['mb-3', 'col-12 col-lg-6' => !$isPdf, 'col-6 is-pdf-picture-col' => $isPdf])>
                                            @include('app.avaliation.partials.photoInput', [
                                                'MODEL' => $Avaliation,
                                                'FIELD_NAME' => $item['fieldName'],
                                                'INPUT_NAME' => $item['inputName'],
                                                'INPUT_DEFAULT_IMAGE' => $item['defaultImg'],
                                                'IMG_ALT' => $item['imgAlt'],
                                                'CAN_EDIT' => false,
                                            ])
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

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
