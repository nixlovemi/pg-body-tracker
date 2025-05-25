@inject('Constants', 'App\Helpers\Constants')
@inject('SysUtils', 'App\Helpers\SysUtils')

@php
/*
View variables:
===============
    - $PAGE_TITLE: string
*/
$USER = $SysUtils::getLoggedInUser() ?? null;
@endphp

@extends('layout.dashboard', [
    'PAGE_TITLE' => $PAGE_TITLE ?? ''
])

@section('DASH_BODY_CONTENT')
    <h4>{{ $PAGE_TITLE }}</h4>

    <form id="user-profile-form" action="{{ route('app.user.doProfile') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <x-card title="{{ __('messages.models.UserInfo.name') }}">
            <div class="row">
                <div class="col-12 col-md-2 text-center">
                    <style>
                        .raf-photo-url {
                            aspect-ratio: 1 / 1;
                            border: none;
                            max-width: 200px;
                            max-height: 200px;
                            min-width: 88px;
                            min-height: 88px;
                            position: relative;
                            margin: 0 auto;
                        }
                    </style>

                    <div class="form-group">
                        @include('app.avaliation.partials.photoInput', [
                            'MODEL' => $USER,
                            'FIELD_NAME' => 'picture_url',
                            'INPUT_NAME' => 'f-user-picture',
                            'INPUT_DEFAULT_IMAGE' => $Constants::USER_DEFAULT_IMAGE_PATH,
                            'IMG_ALT' => __('messages.models.User.fields.pictureUrl'),
                            'CUSTOM_CLASS' => 'img-profile rounded-circle',
                            'CAN_EDIT' => true,
                        ])
                    </div>
                </div>
                <div class="col-12 col-md-10">
                    <div class="form-row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label class="form-label" title="{{ __('messages.models.User.fields.name') }}">
                                    * {{ __('messages.models.User.fields.name') }}
                                </label>
                                @php
                                $value = old("f-user-name") ?: $USER?->first_name;
                                @endphp
                                <input type="text" class="form-control form-control-user"
                                    id="f-user-name" name="f-user-name" maxlength="60"
                                    value="{{ $value }}"
                                />
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label class="form-label" title="{{ __('messages.models.User.fields.lastName') }}">
                                    * {{ __('messages.models.User.fields.lastName') }}
                                </label>
                                @php
                                $value = old("f-user-lname") ?: $USER?->last_name;
                                @endphp
                                <input type="text" class="form-control form-control-user"
                                    id="f-user-lname" name="f-user-lname" maxlength="80"
                                    value="{{ $value }}"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label class="form-label" title="{{ __('messages.pages.client.table.colEmail') }}">
                                    {{ __('messages.pages.client.table.colEmail') }}
                                </label>
                                @php
                                $value = old("f-user-email") ?: $USER?->email;
                                @endphp
                                <input type="text" class="form-control form-control-user"
                                    id="f-user-email" name="f-user-email" maxlength="255"
                                    value="{{ $value }}" readonly
                                />
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label class="form-label" title="{{ __('messages.models.User.fields.active') }}">
                                    {{ __('messages.models.User.fields.active') }}
                                </label>
                                @php
                                $value = old("f-user-active") ?: $USER?->active;
                                $value = $value ? __('messages.yes') : __('messages.no');
                                @endphp
                                <input type="text" class="form-control form-control-user"
                                    id="f-user-active" name="f-user-active" maxlength="10"
                                    value="{{ $value }}" readonly
                                />
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label class="form-label" title="{{ __('messages.pages.profile.userSince') }}">
                                    {{ __('messages.pages.profile.userSince') }}
                                </label>
                                @php
                                $value = old("f-user-since") ?: $USER?->getFormattedCreatedAt();
                                @endphp
                                <input type="text" class="form-control form-control-user"
                                    id="f-user-since" name="f-user-since" maxlength="10"
                                    value="{{ $value }}" readonly
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <p class="mb-0 mt-2">
                <small class="text-muted">
                    {{ __('messages.pages.avaliation.modalAddAvaliation.requiredFieldsInfo') }}
                </small>
            </p>
        </x-card>

        <x-card title="{{ __('messages.pages.profile.cardMoreInfo') }}">
            <div class="row">
                <div class="col-12 col-md-2 text-center">
                    <div class="form-group">
                        @include('app.avaliation.partials.photoInput', [
                            'MODEL' => $USER?->info,
                            'FIELD_NAME' => 'logo_url',
                            'INPUT_NAME' => 'f-userinfo-logo',
                            'INPUT_DEFAULT_IMAGE' => $Constants::USER_LOGO_DEFAULT_IMAGE_PATH,
                            'IMG_ALT' => '',
                            'CAN_EDIT' => true,
                        ])
                    </div>
                </div>
                <div class="col-12 col-md-10">
                    <div class="form-row">
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label class="form-label" title="{{ __('messages.models.UserInfo.fields.title') }}">
                                    {{ __('messages.models.UserInfo.fields.title') }}
                                </label>
                                @php
                                $value = old("f-userinfo-title") ?: $USER?->info?->title;
                                @endphp
                                <input type="text" class="form-control form-control-user"
                                    id="f-userinfo-title" name="f-userinfo-title" maxlength="60"
                                    value="{{ $value }}" placeholder="{{ __('messages.pages.profile.userInfoTitlePlaceholder') }}"
                                />
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label class="form-label" title="{{ __('messages.models.UserInfo.fields.license_text') }}">
                                    {{ __('messages.models.UserInfo.fields.license_text') }}
                                </label>
                                @php
                                $value = old("f-userinfo-lictext") ?: $USER?->info?->license_text;
                                @endphp
                                <input type="text" class="form-control form-control-user"
                                    id="f-userinfo-lictext" name="f-userinfo-lictext" maxlength="60"
                                    value="{{ $value }}" placeholder="{{ __('messages.pages.profile.userInfoLicensePlaceholder') }}"
                                />
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="form-group">
                                <label class="form-label" title="{{ __('messages.models.UserInfo.fields.whatsapp_phone') }}">
                                    {{ __('messages.models.UserInfo.fields.whatsapp_phone') }}
                                </label>
                                @php
                                $value = old("f-userinfo-whats") ?: $USER?->info?->whatsapp_phone;
                                @endphp
                                <input type="text" class="form-control form-control-user"
                                    id="f-userinfo-whats" name="f-userinfo-whats" maxlength="35"
                                    value="{{ $value }}"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label class="form-label" title="{{ __('messages.models.UserInfo.fields.link_telegram') }}">
                                    {{ __('messages.models.UserInfo.fields.link_telegram') }}
                                </label>
                                @php
                                $value = old("f-userinfo-telegram") ?: $USER?->info?->link_telegram;
                                @endphp
                                <input type="text" class="form-control form-control-user"
                                    id="f-userinfo-telegram" name="f-userinfo-telegram" maxlength="100"
                                    value="{{ $value }}"
                                />
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label class="form-label" title="{{ __('messages.models.UserInfo.fields.link_facebook') }}">
                                    {{ __('messages.models.UserInfo.fields.link_facebook') }}
                                </label>
                                @php
                                $value = old("f-userinfo-face") ?: $USER?->info?->link_facebook;
                                @endphp
                                <input type="text" class="form-control form-control-user"
                                    id="f-userinfo-face" name="f-userinfo-face" maxlength="100"
                                    value="{{ $value }}"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label class="form-label" title="{{ __('messages.models.UserInfo.fields.link_instagram') }}">
                                    {{ __('messages.models.UserInfo.fields.link_instagram') }}
                                </label>
                                @php
                                $value = old("f-userinfo-insta") ?: $USER?->info?->link_instagram;
                                @endphp
                                <input type="text" class="form-control form-control-user"
                                    id="f-userinfo-insta" name="f-userinfo-insta" maxlength="100"
                                    value="{{ $value }}"
                                />
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label class="form-label" title="{{ __('messages.models.UserInfo.fields.link_twitter') }}">
                                    {{ __('messages.models.UserInfo.fields.link_twitter') }}
                                </label>
                                @php
                                $value = old("f-userinfo-twit") ?: $USER?->info?->link_twitter;
                                @endphp
                                <input type="text" class="form-control form-control-user"
                                    id="f-userinfo-twit" name="f-userinfo-twit" maxlength="100"
                                    value="{{ $value }}"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label class="form-label" title="{{ __('messages.models.UserInfo.fields.link_youtube') }}">
                                    {{ __('messages.models.UserInfo.fields.link_youtube') }}
                                </label>
                                @php
                                $value = old("f-userinfo-yt") ?: $USER?->info?->link_youtube;
                                @endphp
                                <input type="text" class="form-control form-control-user"
                                    id="f-userinfo-yt" name="f-userinfo-yt" maxlength="100"
                                    value="{{ $value }}"
                                />
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label class="form-label" title="{{ __('messages.models.UserInfo.fields.link_website') }}">
                                    {{ __('messages.models.UserInfo.fields.link_website') }}
                                </label>
                                @php
                                $value = old("f-userinfo-site") ?: $USER?->info?->link_website;
                                @endphp
                                <input type="text" class="form-control form-control-user"
                                    id="f-userinfo-site" name="f-userinfo-site" maxlength="100"
                                    value="{{ $value }}"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <p class="mb-0 mt-2">
                <small class="text-muted">
                    {{ __('messages.pages.profile.cardMoreInfoObsText') }}
                </small>
            </p>
        </x-card>

        <div class="form-actions">
            <div class="text-right">
                <button type="submit" class="btn primary btn-user">{{ __('messages.buttonSave') }}</button>
            </div>
        </div>
    </form>
@endsection
