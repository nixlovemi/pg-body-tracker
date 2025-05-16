@php
/*
View variables:
===============
    - $H1_TEXT: string
    - $H2_TEXT: string
    - $P_TEXT: string
    - $BUTTON_TEXT: string
    - $BUTTON_URL: string
*/
@endphp

@include('layout.partials.login-css')

<div class="container">
    <!-- Outer Row -->
    <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-12 col-md-9">
            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">
                    <div class="row" id="login-info-holder">
                        <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>

                        <div class="col-lg-6 text-center d-flex align-items-center justify-content-center">
                            <div class="p-5">
                                <h1 class="display-1 text-danger font-weight-bold">
                                    {{ $H1_TEXT ?? '404' }}
                                </h1>
                                <h2 class="h4 text-gray-900 mb-4">
                                    {{ $H2_TEXT ?? __('messages.pages.404.title') }}
                                </h2>
                                <p class="mb-4">
                                    {{ $P_TEXT ?? __('messages.pages.404.message') }}
                                </p>
                                <a href="{{ $BUTTON_URL ?? route('app.dashboard.index') }}" class="btn btn-primary btn-user btn-block">
                                    <i class="fas fa-home"></i> {{ $BUTTON_TEXT ?? __('messages.pages.404.buttonBackToHome') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
