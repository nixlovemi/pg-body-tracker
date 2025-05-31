@inject('ARPresenter', 'App\Presenters\AvaliationReportPresenter')
@inject('AvaliationPictures', 'App\Helpers\Feature\AvaliationPictures')

@php
/*
View variables:
===============
    - $AVALIATION: Avaliation
    - $DIV_PHOTO_INPUT_CLASSES: string
*/
$APicFeature = new $AvaliationPictures();
@endphp

@if ($APicFeature->validate())
    <div class="row">
        <div class="col-12">
            <div class="card border-left-secondary shadow py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-sm font-weight-bold text-secondary text-uppercase mb-1">
                                {{ __('messages.pages.avaliation.modalAddAvaliation.pageSixTitle') }}
                            </div>

                            <div class="row">
                                @foreach ($ARPresenter::getImagesData() as $item)
                                    <div class="{{ $DIV_PHOTO_INPUT_CLASSES }}">
                                        @include('app.avaliation.partials.photoInput', [
                                            'MODEL' => $AVALIATION,
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
@endif
