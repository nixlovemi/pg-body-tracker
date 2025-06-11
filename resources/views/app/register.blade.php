@inject('Icons', 'App\Helpers\Icons')

@php
/*
View variables:
===============
    - $PAGE_TITLE: string
    - $PREMIUM_FLOW: boolean
*/

$PREMIUM_FLOW = $PREMIUM_FLOW ?? false;
$pMessage = $PREMIUM_FLOW
    ? __('messages.pages.login.register.descriptionPremium')
    : __('messages.pages.login.register.description');
$formAction = $PREMIUM_FLOW
    ? route('app.doRegisterPremium')
    : route('app.doRegister');
@endphp

@extends('layout.login-base', [
    'PAGE_TITLE' => $PAGE_TITLE ?? ''
])

@section('LOGIN_BASE_CONTENT')
    <p>
        {{ __($pMessage) }}
    </p>

    <form action="{{ $formAction }}" class="user" method="post" data-premium="{{ $PREMIUM_FLOW ? '1' : '0' }}">
        @if ($PREMIUM_FLOW)
            <h6 style="font-weight:bold">{{ __('messages.pages.login.register.choosePremiumPlan') }}</h6>
            <style>
                #f-subscriptionType {
                    border-radius: .35rem;
                    padding: .375rem .75rem;
                }
            </style>

            @include('app.subscription.partials.premium-select')
            <hr />
        @endif

        <a href="{{ route('app.googleLogin') }}" class="btn btn-google btn-user btn-block">
            {!! $Icons::GOOGLE !!}
            {{ __('messages.pages.login.loginGoogle') }}
        </a>
        <hr />

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

    <script>
        document.querySelector('.btn-google').addEventListener('click', function(event) {
            const subscriptionType = document.getElementById('f-subscriptionType');
            if (subscriptionType) {
                const selectedPlan = subscriptionType.value;
                const url = new URL(this.href);
                url.searchParams.set('plan', selectedPlan);
                this.href = url.toString();
            }
        });
    </script>
@endsection
