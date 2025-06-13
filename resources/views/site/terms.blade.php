@extends('layout.site')

@section('SITE_BODY_CONTENT')
    @include('site.partials.other-pages-css')

    <section id="home" class="hero-section">
        <div class="container">
            <div class="row align-items-center position-relative">
                <div class="col-10 offset-1">
                    <div class="hero-content">
                    <h1 class="wow fadeInUp text-center" data-wow-delay=".4s" style="visibility: visible; animation-delay: 0.4s; animation-name: fadeInUp;">
                        {{ __('messages.pages.siteTerms.title') }}
                    </h1>
                    <h2 class="text-white text-center mb-4 wow fadeInUp" data-wow-delay=".2s">
                        {{ __('messages.pages.siteTerms.h2') }}
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
                                {{ __('messages.pages.siteTerms.intro') }}
                            </p>
                            <small class="text-muted d-block mt-3">
                                {{ __('messages.pages.siteTerms.updatedAt', ['date' => now()->format('d/m/Y')]) }}
                            </small>
                        </div>
                    </div>
                </div>

                {{-- Conteúdo centralizado --}}
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="policy-content">
                            <div class="row content-line">
                                <h3>{{ __('messages.pages.siteTerms.section1Title') }}</h3>
                                <p>
                                    {!! __('messages.pages.siteTerms.section1Text', [
                                        'app' => env('APP_NAME'),
                                        'privacyUrl' => route('site.privacy')
                                    ]) !!}
                                </p>
                            </div>

                            <div class="row content-line">
                                <h3>{{ __('messages.pages.siteTerms.section2Title') }}</h3>
                                <p>{{ __('messages.pages.siteTerms.section2Text') }}</p>
                            </div>

                            <div class="row content-line">
                                <h3>{{ __('messages.pages.siteTerms.section3Title') }}</h3>
                                <p>{{ __('messages.pages.siteTerms.section3Text') }}</p>
                            </div>

                            <div class="row content-line">
                                <h3>{{ __('messages.pages.siteTerms.section4Title') }}</h3>
                                <p>{{ __('messages.pages.siteTerms.section4Text') }}</p>
                                <ul>
                                    <li>{{ __('messages.pages.siteTerms.section4List1') }}</li>
                                    <li>{{ __('messages.pages.siteTerms.section4List2') }}</li>
                                    <li>{{ __('messages.pages.siteTerms.section4List3') }}</li>
                                </ul>
                            </div>

                            <div class="row content-line">
                                <h3>{{ __('messages.pages.siteTerms.section5Title') }}</h3>
                                <p>{{ __('messages.pages.siteTerms.section5Text', ['app' => env('APP_NAME')]) }}</p>
                                <ul>
                                    <li>{{ __('messages.pages.siteTerms.section5List1') }}</li>
                                    <li>{{ __('messages.pages.siteTerms.section5List2') }}</li>
                                    <li>{{ __('messages.pages.siteTerms.section5List3') }}</li>
                                </ul>
                            </div>

                            <div class="row content-line">
                                <h3>{{ __('messages.pages.siteTerms.section6Title') }}</h3>
                                <p>{{ __('messages.pages.siteTerms.section6Text') }}</p>
                            </div>

                            <div class="row content-line">
                                <h3>{{ __('messages.pages.siteTerms.section7Title') }}</h3>
                                <p>{{ __('messages.pages.siteTerms.section7Text') }}</p>
                            </div>

                            <div class="row content-line">
                                <h3>{{ __('messages.pages.siteTerms.section8Title') }}</h3>
                                <p>{{ __('messages.pages.siteTerms.section8Text', ['app' => env('APP_NAME')]) }}</p>
                            </div>

                            <div class="row content-line">
                                <h3>{{ __('messages.pages.siteTerms.section9Title') }}</h3>
                                <p>{{ __('messages.pages.siteTerms.section9Text') }}</p>
                            </div>

                            <div class="row content-line">
                                <h3>{{ __('messages.pages.siteTerms.section10Title') }}</h3>
                                <p>{{ __('messages.pages.siteTerms.section10Text') }}</p>
                            </div>

                            <div class="row content-line">
                                <h3>{{ __('messages.pages.siteTerms.section11Title') }}</h3>
                                <p>
                                    {!! __('messages.pages.siteTerms.section11Text', ['email' => env('SUPPORT_EMAIL')]) !!}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
