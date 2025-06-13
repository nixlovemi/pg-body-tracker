@inject('LayoutSitePresenter', 'App\Presenters\LayoutSitePresenter')

@extends('layout.site')

@section('SITE_BODY_CONTENT')
    @include('site.partials.other-pages-css')

    <section id="home" class="hero-section">
        <div class="container">
            <div class="row align-items-center position-relative">
                <div class="col-10 offset-1">
                    <div class="hero-content">
                    <h1 class="wow fadeInUp text-center" data-wow-delay=".4s" style="visibility: visible; animation-delay: 0.4s; animation-name: fadeInUp;">
                        {{ __('messages.pages.siteFaq.title') }}
                    </h1>
                    <h2 class="text-white text-center mb-4 wow fadeInUp" data-wow-delay=".2s">
                        {{ __('messages.pages.siteFaq.h2') }}
                    </h2>
                </div>
            </div>
        </div>
    </section>

    <section id="page-content" class="feature-section pt-60">
        <div class="feature-extended-wrapper">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xxl-6 col-xl-7 col-lg-9 col-md-10">
                        <div class="section-title text-center mb-60">
                            <p class="text-muted">
                                {!! __('messages.pages.siteFaq.description', ['email' => env('SUPPORT_EMAIL')]) !!}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="accordion" id="faqAccordion">
                            @foreach ($LayoutSitePresenter::getFaq() as $faq)
                                @php $idx = $loop->index + 1; @endphp

                                <div class="accordion-item mb-3">
                                    <h3 class="accordion-header" id="faqHeading{{$idx}}">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse{{$idx}}" aria-expanded="true" aria-controls="faqCollapse{{$idx}}">
                                            {{ $faq['question'] }}
                                        </button>
                                    </h3>
                                    <div id="faqCollapse{{$idx}}" class="accordion-collapse collapse {{ $idx == 1 ? 'show' : 'hide' }}" aria-labelledby="faqHeading{{$idx}}" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body text-muted">
                                            {{ $faq['answer'] }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
