@extends('layout.site')

@section('SITE_BODY_CONTENT')
    @include('site.partials.other-pages-css')

    <section id="home" class="hero-section">
        <div class="container">
            <div class="row align-items-center position-relative">
                <div class="col-10 offset-1">
                    <div class="hero-content">
                    <h1 class="wow fadeInUp text-center" data-wow-delay=".4s" style="visibility: visible; animation-delay: 0.4s; animation-name: fadeInUp;">
                        {{ __('messages.pages.sitePrivacy.title') }}
                    </h1>
                    <h2 class="text-white text-center mb-4 wow fadeInUp" data-wow-delay=".2s">
                        {{ __('messages.pages.sitePrivacy.h2') }}
                    </h2>
                </div>
            </div>
        </div>
    </section>

    <section id="page-content" class="feature-section pt-50">
        <div class="feature-extended-wrapper">
            <div class="container">
                {{-- Título centralizado --}}
                <div class="row justify-content-center">
                    <div class="col-xxl-6 col-xl-7 col-lg-9 col-md-10">
                        <div class="section-title text-center mb-60">
                            <p class="wow fadeInUp" data-wow-delay=".4s">
                                {{ __('messages.pages.sitePrivacy.intro') }}
                            </p>
                            <small class="text-muted d-block mt-3">
                                {{ __('messages.pages.sitePrivacy.updatedAt', ['date' => now()->format('d/m/Y')]) }}
                            </small>
                        </div>
                    </div>
                </div>

                {{-- Conteúdo centralizado --}}
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="row content-line">
                            <h3>{{ __('messages.pages.sitePrivacy.section1Title') }}</h3>
                            <p class="text-muted">
                                {{ __('messages.pages.sitePrivacy.section1Text', ['app' => env('APP_NAME')]) }}
                            </p>
                        </div>

                        <div class="row content-line">
                            <h3>{{ __('messages.pages.sitePrivacy.section2Title') }}</h3>
                            <p class="text-muted">
                                {{ __('messages.pages.sitePrivacy.section2Text') }}
                            </p>
                            <ul class="text-muted">
                                <li>{{ __('messages.pages.sitePrivacy.section2List1') }}</li>
                                <li>{{ __('messages.pages.sitePrivacy.section2List2') }}</li>
                                <li>{{ __('messages.pages.sitePrivacy.section2List3') }}</li>
                                <li>{{ __('messages.pages.sitePrivacy.section2List4') }}</li>
                                <li>{{ __('messages.pages.sitePrivacy.section2List5') }}</li>
                            </ul>
                        </div>

                        <div class="row content-line">
                            <h3>{{ __('messages.pages.sitePrivacy.section3Title') }}</h3>
                            <p class="text-muted">
                                {{ __('messages.pages.sitePrivacy.section3Text') }}
                            </p>
                            <ul class="text-muted">
                                <li>{{ __('messages.pages.sitePrivacy.section3List1') }}</li>
                                <li>{{ __('messages.pages.sitePrivacy.section3List2') }}</li>
                                <li>{{ __('messages.pages.sitePrivacy.section3List3') }}</li>
                                <li>{{ __('messages.pages.sitePrivacy.section3List4') }}</li>
                                <li>{{ __('messages.pages.sitePrivacy.section3List5') }}</li>
                            </ul>
                        </div>

                        <div class="row content-line">
                            <h3>{{ __('messages.pages.sitePrivacy.section4Title') }}</h3>
                            <p class="text-muted">
                                {{ __('messages.pages.sitePrivacy.section4Text') }}
                            </p>
                            <ul class="text-muted">
                                <li>{{ __('messages.pages.sitePrivacy.section4List1') }}</li>
                                <li>{{ __('messages.pages.sitePrivacy.section4List2') }}</li>
                            </ul>
                        </div>

                        <div class="row content-line">
                            <h3>{{ __('messages.pages.sitePrivacy.section5Title') }}</h3>
                            <p class="text-muted">
                                {{ __('messages.pages.sitePrivacy.section5Text1') }}
                            </p>
                            <p class="text-muted">
                                {{ __('messages.pages.sitePrivacy.section5Text2') }}
                            </p>
                        </div>

                        <div class="row content-line">
                            <h3>{{ __('messages.pages.sitePrivacy.section6Title') }}</h3>
                            <p class="text-muted">
                                {{ __('messages.pages.sitePrivacy.section6Text') }}
                            </p>
                        </div>

                        <div class="row content-line">
                            <h3>{{ __('messages.pages.sitePrivacy.section7Title') }}</h3>
                            <p class="text-muted">
                                {{ __('messages.pages.sitePrivacy.section7Text') }}
                            </p>
                            <ul class="text-muted">
                                <li>{{ __('messages.pages.sitePrivacy.section7List1') }}</li>
                                <li>{{ __('messages.pages.sitePrivacy.section7List2') }}</li>
                                <li>{{ __('messages.pages.sitePrivacy.section7List3') }}</li>
                                <li>{{ __('messages.pages.sitePrivacy.section7List4') }}</li>
                            </ul>
                        </div>

                        <div class="row content-line">
                            <h3>{{ __('messages.pages.sitePrivacy.section8Title') }}</h3>
                            <p class="text-muted">
                                {{ __('messages.pages.sitePrivacy.section8Text') }}
                            </p>
                        </div>

                        <div class="row content-line">
                            <h3>{{ __('messages.pages.sitePrivacy.section9Title') }}</h3>
                            <p class="text-muted">
                                {{ __('messages.pages.sitePrivacy.section9Text') }}
                            </p>
                        </div>

                        <div class="row content-line">
                            <h3>{{ __('messages.pages.sitePrivacy.section10Title') }}</h3>
                            <p class="text-muted">
                                {!! __('messages.pages.sitePrivacy.section10Text', ['email' => env('SUPPORT_EMAIL')]) !!}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
