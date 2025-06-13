<!-- ======== about-section start ======== -->
<section id="about" class="about-section pt-150">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-xl-6 col-lg-6">
                <div class="about-img">
                    <img src="/images/site-about.webp" alt="Tela do PG BodyTracker com gráficos e relatórios de avaliação física" class="w-100">
                    <img src="{{ url('/') }}/template/main-site-saaspal-free-lite/assets/img/about/about-left-shape.svg" alt="" role="presentation" class="shape shape-1">
                    <img src="{{ url('/') }}/template/main-site-saaspal-free-lite/assets/img/about/left-dots.svg" alt="" role="presentation" class="shape shape-2">
                </div>
            </div>
            <div class="col-xl-6 col-lg-6">
                <div class="about-content">
                    <div class="section-title mb-30">
                        <h2 class="mb-25 wow fadeInUp" data-wow-delay=".2s">
                            {{ __('messages.pages.siteHome.aboutSection.title') }}
                        </h2>
                        <p class="wow fadeInUp" data-wow-delay=".4s">
                            {{ __('messages.pages.siteHome.aboutSection.text') }}
                        </p>
                    </div>

                    <a href="{{ route('app.register') }}" class="main-btn btn-hover border-btn wow fadeInUp" data-wow-delay=".6s">
                        {{ __('messages.pages.siteHome.aboutSection.ctaButton') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- ======== about-section end ======== -->

<!-- ======== about2-section start ======== -->
<section id="about-2" class="about-section pt-150">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-xl-6 col-lg-6">
                <div class="about-content">
                    <div class="section-title mb-30">
                        <h2 class="mb-25 wow fadeInUp" data-wow-delay=".2s">
                            {{ __('messages.pages.siteHome.aboutSection.title2') }}
                        </h2>
                        <p class="wow fadeInUp" data-wow-delay=".4s">
                            {{ __('messages.pages.siteHome.aboutSection.text2') }}
                        </p>
                    </div>
                    <ul>
                        <li>{{ __('messages.pages.siteHome.aboutSection.li2-1') }}</li>
                        <li>{{ __('messages.pages.siteHome.aboutSection.li2-2') }}</li>
                        <li>{{ __('messages.pages.siteHome.aboutSection.li2-3') }}</li>
                    </ul>
                    <a href="{{ route('app.register') }}" class="main-btn btn-hover border-btn wow fadeInUp" data-wow-delay=".6s">
                        {{ __('messages.pages.siteHome.aboutSection.ctaButton2') }}
                    </a>
                </div>
            </div>
            <div class="col-xl-6 col-lg-6 order-first order-lg-last">
                <div class="about-img-2">
                    <img src="/images/site-about-2.webp" alt="Interface do sistema PG BodyTracker com funcionalidades organizadas e intuitivas" class="w-100">
                    <img src="{{ url('/') }}/template/main-site-saaspal-free-lite/assets/img/about/about-right-shape.svg" alt="" role="presentation" class="shape shape-1">
                    <img src="{{ url('/') }}/template/main-site-saaspal-free-lite/assets/img/about/right-dots.svg" alt="" role="presentation" class="shape shape-2">
                </div>
            </div>
        </div>
    </div>
</section>
<!-- ======== about2-section end ======== -->
