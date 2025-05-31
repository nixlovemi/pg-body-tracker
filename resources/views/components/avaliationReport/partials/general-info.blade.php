@inject('ARPresenter', 'App\Presenters\AvaliationReportPresenter')
@inject('UserReportLogo', 'App\Helpers\Feature\UserReportLogo')

@php
/*
View variables:
===============
    - $AVALIATION: Avaliation
    - $DIV_ROW_CLASSES: string
    - $DIV_COL_CLASSES: string
    - $DIV_CARD_CLASSES: string
    - $DIV_USER_INFO_CLASSES: string
    - $ABLE_SOCIAL_1_CLASSES: string
    - $TABLE_SOCIAL_2_CLASSES: string
    - $DIV_COL_CLIENT_INFO_CLASSES: string
    - $DIV_CLIENT_INFO_EXTRA_SPACE: bool
    - $DIV_COL_PROGRESS_CLASSES: string
    - $PROGRESS_BAR_VIEW_NAME: string
*/

$URLogoFeature = new $UserReportLogo();
@endphp

<div class="avaliation-report-general-info {{ $DIV_ROW_CLASSES }}">
    <div class="user-info {{ $DIV_COL_CLASSES }}">
        <div class="{{ $DIV_CARD_CLASSES }}">
            <div class="card-body">
                <div class="row">
                    <div class="col text-left">
                        @if ($URLogoFeature->validate())
                            <div id="uinfo-logo" class="w-25 float-left">
                                <img class="img-fluid" src="{{ $ARPresenter::getUserLogoBase64($Avaliation) }}" />
                            </div>
                        @endif
                        <div id="uinfo-info" @class(['float-left pl-3 w-75', 'w-100' => !$URLogoFeature->validate()])>
                            <div class="text-sm font-weight-bold text-secondary text-uppercase mb-1">
                                {{ __('messages.pages.avaliation.viewReport.contact') }}: {{ $Avaliation->client->user->getFullName() }}
                            </div>

                            <div class="{{ $DIV_USER_INFO_CLASSES }}">
                                {{ $Avaliation->client->user->email }}
                            </div>

                            @foreach ($ARPresenter::getUserInfoLoopArray($Avaliation) as $item)
                                <div class="{{ $DIV_USER_INFO_CLASSES }}">
                                    {{ $item['value'] }}
                                </div>
                            @endforeach

                            <!-- social -->
                            <div class="{{ $TABLE_SOCIAL_1_CLASSES }}">
                                <table class="table-social-1 table table-borderless">
                                    <tbody>
                                        @foreach (array_chunk($ARPresenter::getSocialLinks($Avaliation) ?? [], 2) as $row)
                                            <tr class="border-bottom">
                                                @foreach ($row as $item)
                                                    <td class="align-middle" scope="row">
                                                        <i class="{{ $item['icon'] }}"></i>&nbsp;
                                                        {{ $item['value'] }}
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="{{ $TABLE_SOCIAL_2_CLASSES }}">
                                <table class="table-social-2 table table-borderless" style="">
                                    <tbody>
                                        @foreach ($ARPresenter::getSocialLinks($Avaliation) as $item)
                                            <tr class="border-bottom">
                                                <td style="text-align:center;" class="align-middle" scope="row">
                                                    <i class="{{ $item['icon'] }}"></i>&nbsp;
                                                    {{ $item['value'] }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="client-info {{ $DIV_COL_CLIENT_INFO_CLASSES }}">
        <div class="{{ $DIV_CARD_CLASSES }}">
            <div class="card-body">
                @include('components.avaliationReport.partials.client-info', [
                    'AVALIATION' => $Avaliation,
                ])

                @if ($DIV_CLIENT_INFO_EXTRA_SPACE ?? false)
                    <!-- matching size with Progress card -->
                    &nbsp;<br />
                @endif
            </div>
        </div>
    </div>

    <div class="client-progress {{ $DIV_COL_PROGRESS_CLASSES }}">
        <div class="{{ $DIV_CARD_CLASSES }}">
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

                        @include($PROGRESS_BAR_VIEW_NAME, [
                            'AVALIATION' => $Avaliation
                        ])
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
