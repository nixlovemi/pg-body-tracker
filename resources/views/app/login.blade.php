@inject('Icons', 'App\Helpers\Icons')

@php
/*
View variables:
===============
    - $PAGE_TITLE: string
*/
@endphp

@extends('layout.core', [
    'PAGE_TITLE' => $PAGE_TITLE ?? ''
])

@section('CORE_HEADER_CUSTOM_CSS')
    @include('layout.partials.login-css')
@endsection

@section('CORE_BODY_CONTENT')
    <div class="container">
        <!-- Outer Row -->
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row" id="login-info-holder">
                            <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>

                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center mb-4">
                                        <img class="img-fluid" src="/images/logo-azul.png" alt="PG Body Tracker" />
                                    </div>

                                    @include('layout.partials.alert-return-messages')

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
                                        <a class="small" href="#">
                                            {{ __('messages.pages.login.forgotPassword') }}
                                        </a>
                                    </div>

                                    @php
                                    /*
                                    <div class="text-center">
                                        <a class="small" href="register.html">Create an Account!</a>
                                    </div>
                                    */
                                    @endphp
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('CORE_FOOTER_CUSTOM_JS')
@endsection
