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
                                        <a href="{{ route('site.home') }}">
                                            <img class="img-fluid" src="/images/logo-azul.png" alt="PG Body Tracker" />
                                        </a>
                                    </div>

                                    @include('layout.partials.alert-return-messages')

                                    @yield('LOGIN_BASE_CONTENT')
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
