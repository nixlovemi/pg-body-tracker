@inject('Icons', 'App\Helpers\Icons')

@php
/*
View variables:
===============
    - $PAGE_TITLE: string
*/
@endphp

@extends('layout.login-base', [
    'PAGE_TITLE' => $PAGE_TITLE ?? ''
])

@section('LOGIN_BASE_CONTENT')
    <form action="{{ route('app.doLogin') }}" class="user" method="post">
        @csrf
        <div class="form-group">
            <input type="email" class="form-control form-control-user"
                id="f-email" name="f-email" aria-describedby="emailHelp"
                placeholder="{{ __('messages.pages.login.emailPlaceholder') }}"
            />
        </div>
        <div class="form-group">
            <input type="password" class="form-control form-control-user"
                id="f-password" name="f-password" placeholder="{{ __('messages.pages.login.passwordPlaceholder') }}"
            />
        </div>

        <button type="submit" class="btn primary btn-user btn-block">
            {{ __('messages.pages.login.loginButton') }}
        </button>

        @if (app()->environment('local'))
            <hr />
            <a href="#" class="btn btn-google btn-user btn-block">
                {!! $Icons::GOOGLE !!}
                {{ __('messages.pages.login.loginGoogle') }}
            </a>
        @endif
    </form>

    <hr />
    <div class="text-center">
        <a class="" href="{{  route('app.forgot') }}">
            {!! $Icons::KEY_GREY !!}
            {{ __('messages.pages.login.forgotPassword') }}
        </a>
        <br />
        <a class="" href="{{  route('app.register') }}">
            <span class="text-gray-400 mr-1">{!! $Icons::USER_PLUS !!}</span>
            {{ __('messages.pages.login.newAccount') }}
        </a>
    </div>

    @php
    /*
    <div class="text-center">
        <a class="small" href="register.html">Create an Account!</a>
    </div>
    */
    @endphp
@endsection
