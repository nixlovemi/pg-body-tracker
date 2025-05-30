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
        {{ __('messages.pages.login.forgot.description')}}
    </p>

    <form action="{{ route('app.doForgot') }}" class="user" method="post">
        @csrf
        <div class="form-group">
            <input type="email" class="form-control form-control-user"
                id="f-email" name="f-email" aria-describedby="emailHelp"
                value="{{ old('f-email') }}"
                placeholder="{{ __('messages.pages.login.emailPlaceholder') }}"
            />
        </div>

        <button type="submit" class="btn primary btn-user btn-block">
            {{ __('messages.pages.login.forgot.sendButton') }}
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
