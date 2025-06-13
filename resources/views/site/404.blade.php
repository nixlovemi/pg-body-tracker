@extends('layout.site', [
    'PAGE_TITLE' => __('messages.pages.404.title'),
])

@section('SITE_BODY_CONTENT')
    <style>
        .error-section {
            padding: 100px 0;
            text-align: center;
        }
        .error-title {
            font-size: 100px;
            font-weight: 800;
            color: #555;
        }
        .error-message {
            font-size: 20px;
            color: #777;
        }
        .error-button {
            margin-top: 30px;
        }
    </style>

    <section id="home" class="hero-section">
        <div class="container">
            <div class="row align-items-center position-relative">
                <div class="col-10 offset-1">
                    <div class="hero-content">
                    <h1 class="wow fadeInUp text-center" data-wow-delay=".4s" style="visibility: visible; animation-delay: 0.4s; animation-name: fadeInUp;">
                        {{ __('messages.pages.404.title') }}
                    </h1>
                </div>
            </div>
        </div>
    </section>

    <!-- Error Section -->
    <section class="error-section">
        <div class="container">
            <div class="row justify-content-center align-items-center">
                <div class="col-lg-8">
                    <div class="text-center">
                        <img src="/images/site-404.jpg" alt="PG Body Tracker - Página não encontrada" class="img-fluid">
                    </div>

                    <p class="error-message">
                        {!! __('messages.pages.site404.errorMsg') !!}
                    </p>

                    <a href="{{ route('site.home') }}" class="btn btn-primary btn-hover error-button">{{ __('messages.pages.404.buttonBackToHome') }}</a>
                </div>
            </div>
        </div>
    </section>
@endsection
