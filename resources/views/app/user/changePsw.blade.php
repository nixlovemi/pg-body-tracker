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

    <form id="user-changePsw-form" action="{{ route('app.user.doChangePsw') }}" method="POST">
        @csrf

        <x-card title="{{ __('messages.models.User.fields.password') }}">
            <div class="row">
                <div class="col-12 col-lg-4">
                    @include('app.passwordRules')
                </div>
                <div class="col-12 col-lg-8">
                    <div class="form-row">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label" title="{{ __('messages.pages.changePsw.currentPassword') }}">
                                    * {{ __('messages.pages.changePsw.currentPassword') }}
                                </label>
                                @php
                                $value = old("f-cur-psw");
                                @endphp
                                <input type="password" class="form-control form-control-user"
                                    id="f-cur-psw" name="f-cur-psw" maxlength="50"
                                    value="{{ $value }}"
                                />
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label" title="{{ __('messages.pages.changePsw.newPassword') }}">
                                    * {{ __('messages.pages.changePsw.newPassword') }}
                                </label>
                                @php
                                $value = old("f-new-psw");
                                @endphp
                                <input type="password" class="form-control form-control-user"
                                    id="f-new-psw" name="f-new-psw" maxlength="50"
                                    value="{{ $value }}"
                                />
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label" title="{{ __('messages.pages.changePsw.confirmNewPassword') }}">
                                    * {{ __('messages.pages.changePsw.confirmNewPassword') }}
                                </label>
                                @php
                                $value = old("f-confirm-new-psw");
                                @endphp
                                <input type="password" class="form-control form-control-user"
                                    id="f-confirm-new-psw" name="f-confirm-new-psw" maxlength="50"
                                    value="{{ $value }}"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </x-card>

        <div class="form-actions">
            <div class="text-right">
                <button type="submit" class="btn primary btn-user">{{ __('messages.buttonSave') }}</button>
            </div>
        </div>
    </form>
@endsection
