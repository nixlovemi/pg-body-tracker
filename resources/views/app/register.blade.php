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
    <p>
        {{ __('messages.pages.login.register.description')}}
    </p>

    <form action="{{ route('app.doRegister') }}" class="user" method="post">
        @csrf
        <div class="form-group">
            <input type="text" class="form-control form-control-user"
                id="f-name" name="f-name" aria-describedby="nameHelp"
                value="{{ old('f-name') }}" maxlength="60"
                placeholder="{{ __('messages.models.Client.fields.first_name') }}"
            />
        </div>

        <div class="form-group">
            <input type="text" class="form-control form-control-user"
                id="f-lastname" name="f-lastname" aria-describedby="nameHelp"
                value="{{ old('f-lastname') }}" maxlength="80"
                placeholder="{{ __('messages.models.Client.fields.last_name') }}"
            />
        </div>

        <div class="form-group">
            <input type="email" class="form-control form-control-user"
                id="f-email" name="f-email" aria-describedby="emailHelp"
                value="{{ old('f-email') }}" maxlength="255"
                placeholder="{{ __('messages.pages.login.emailPlaceholder') }}"
            />
        </div>

        <div class="form-group">
            <input type="password" class="form-control form-control-user" maxlength="80"
                id="f-password" name="f-password" placeholder="{{ __('messages.pages.login.passwordPlaceholder') }}"
            />
        </div>

        @include('app.passwordRules')

        <button type="submit" class="btn primary btn-user btn-block">
            {{ __('messages.pages.login.register.buttonRegister') }}
        </button>
    </form>

    <hr />
    <div class="text-center">
        <a class="" href="{{  route('app.login') }}">
            {!! $Icons::HOME !!}
            {{ __('messages.pages.login.forgot.returnLogin') }}
        </a>
    </div>
@endsection
