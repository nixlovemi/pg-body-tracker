@inject('Icons', 'App\Helpers\Icons')
@inject('URL', 'Illuminate\Support\Facades\URL')

@php
/*
View variables:
===============
    - $PAGE_TITLE: string
    - $ID_KEY: string
*/
@endphp

@extends('layout.login-base', [
    'PAGE_TITLE' => $PAGE_TITLE ?? ''
])

@section('LOGIN_BASE_CONTENT')
    <p>
        {{ __('messages.pages.login.resetPwd.description')}}
    </p>

    <div class="row mb-1">
        <div class="col">
            @include('app.passwordRules')
        </div>
    </div>

    <form action="{{ URL::temporarySignedRoute('app.doResetPwd', now()->addHours(24)) }}" class="user" method="post">
        @csrf
        <input type="hidden" name="f-idkey" value="{{ $ID_KEY }}" />

        <div class="form-group">
            <input type="password" class="form-control form-control-user"
                id="f-password" name="f-password" placeholder="{{ __('messages.pages.changePsw.newPassword') }}"
            />
        </div>

        <div class="form-group">
            <input type="password" class="form-control form-control-user"
                id="f-rtype-password" name="f-rtype-password" placeholder="{{ __('messages.pages.changePsw.confirmNewPassword') }}"
            />
        </div>

        <button type="submit" class="btn primary btn-user btn-block">
            {{ __('messages.pages.login.resetPwd.title') }}
        </button>
    </form>

    <hr />
    <div class="text-center">
        <a class="small" href="{{  route('app.login') }}">
            {!! $Icons::HOME !!}
            {{ __('messages.pages.login.forgot.returnLogin') }}
        </a>
    </div>
@endsection
