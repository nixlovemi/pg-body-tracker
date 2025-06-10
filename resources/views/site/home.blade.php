<!DOCTYPE html>
<html dir="ltr" lang="pt-BR">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="x-ua-compatible" content="ie=edge" />
        <title>{{ __('messages.pages.siteHome.title') }}</title>
        <meta name="description" content="" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png" />
        <!-- Place favicon.ico in the root directory -->

        <!-- ======== CSS here ======== -->
        <link rel="stylesheet" href="{{ url('/') }}/template/main-site-saaspal-free-lite/assets/css/bootstrap.min.css" />
        <link rel="stylesheet" href="{{ url('/') }}/template/main-site-saaspal-free-lite/assets/css/lineicons.css" />
        <link rel="stylesheet" href="{{ url('/') }}/template/main-site-saaspal-free-lite/assets/css/tiny-slider.css" />
        <link rel="stylesheet" href="{{ url('/') }}/template/main-site-saaspal-free-lite/assets/css/animate.css" />
        <link rel="stylesheet" href="{{ url('/') }}/template/main-site-saaspal-free-lite/assets/css/main.css" />
    </head>
    <body>
        <!--[if lte IE 9]>
        <p class="browserupgrade">
            You are using an <strong>outdated</strong> browser. Please
            <a href="https://browsehappy.com/">upgrade your browser</a> to improve
            your experience and security.
        </p>
        <![endif]-->

        <!-- ======== preloader start ======== -->
        <div class="preloader">
            <div class="loader">
                <div class="spinner">
                    <div class="spinner-container">
                        <div class="spinner-rotator">
                            <div class="spinner-left">
                                <div class="spinner-circle"></div>
                            </div>
                            <div class="spinner-right">
                                <div class="spinner-circle"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- preloader end -->

        <!-- ======== header start ======== -->
        <header class="header">
            <div class="navbar-area">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-lg-12">
                            <nav class="navbar navbar-expand-lg">
                                <a class="navbar-brand" href="index.html.htm">
                                    <img style="max-width: initial;" src="/images/site-logo-top-white.png" alt="PG Body Tracker Logo" />
                                </a>
                                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                                    <span class="toggler-icon"></span>
                                    <span class="toggler-icon"></span>
                                    <span class="toggler-icon"></span>
                                </button>

                                <div class="collapse navbar-collapse sub-menu-bar" id="navbarSupportedContent">
                                    <ul id="nav" class="navbar-nav ms-auto">
                                        <li class="nav-item">
                                            <a class="page-scroll active" href="#home">
                                                {{ __('messages.pages.siteHome.header.menuHome') }}
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="page-scroll" href="#features">
                                                {{ __('messages.pages.siteHome.header.menuFeatures') }}
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="page-scroll" href="#about">
                                                {{ __('messages.pages.siteHome.header.menuAbout') }}
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="page-scroll" href="#why">
                                                {{ __('messages.pages.siteHome.header.menuWhyUs') }}
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="page-scroll" href="#versions">
                                                {{ __('messages.pages.siteHome.header.menuVersions') }}
                                            </a>
                                        </li>
                                        @php
                                        /*
                                        <li class="nav-item">
                                            <a class="page-scroll" href="#testimonials">
                                                {{ __('messages.pages.siteHome.header.menuClients') }}
                                            </a>
                                        </li>
                                        */
                                        @endphp
                                    </ul>
                                </div>
                                <!-- navbar collapse -->
                            </nav>
                            <!-- navbar -->
                        </div>
                    </div>
                    <!-- row -->
                </div>
                <!-- container -->
            </div>
            <!-- navbar area -->
        </header>
        <!-- ======== header end ======== -->

        <!-- ======== hero-section start ======== -->
        <section id="home" class="hero-section">
            <div class="container">
                <div class="row align-items-center position-relative">
                    <div class="col-lg-6">
                        <div class="hero-content">
                        <h1 class="wow fadeInUp" data-wow-delay=".4s">
                            {{ __('messages.pages.siteHome.heroSection.title') }}
                        </h1>
                        <p class="wow fadeInUp" data-wow-delay=".6s">
                            {{ __('messages.pages.siteHome.heroSection.description') }}
                        </p>
                        <a href="{{ route('app.register') }}" class="main-btn border-btn btn-hover wow fadeInUp" data-wow-delay=".6s">
                            {{ __('messages.pages.siteHome.heroSection.ctaButton') }}
                        </a>
                        <a href="#features" class="scroll-bottom">
                            <i class="lni lni-arrow-down"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="hero-img wow fadeInUp" data-wow-delay=".5s">
                        <img src="/images/site-hero-top.png" alt="PG Body Tracker Dashboard">
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- ======== hero-section end ======== -->

        <!-- ======== feature-section start ======== -->
        <section id="features" class="feature-section pt-120">
            <div class="container">
                <div class="row justify-content-center">
                    @for ($i=1; $i<=6; $i++)
                        <div class="col-lg-4 col-md-8 col-sm-10">
                            <div class="single-feature">
                                <div class="icon">
                                    <i class="{{ __('messages.pages.siteHome.featuresSection.col'.$i.'Icon') }}"></i>
                                </div>
                                <div class="content">
                                    <h3>{{ __('messages.pages.siteHome.featuresSection.col'.$i.'Title') }}</h3>
                                    <p>
                                        {{ __('messages.pages.siteHome.featuresSection.col'.$i.'Text') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        </section>
        <!-- ======== feature-section end ======== -->

        <!-- ======== about-section start ======== -->
        <section id="about" class="about-section pt-150">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-xl-6 col-lg-6">
                        <div class="about-img">
                            <img src="/images/site-about.png" alt="" class="w-100">
                            <img src="{{ url('/') }}/template/main-site-saaspal-free-lite/assets/img/about/about-left-shape.svg" alt="" class="shape shape-1">
                            <img src="{{ url('/') }}/template/main-site-saaspal-free-lite/assets/img/about/left-dots.svg" alt="" class="shape shape-2">
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
        <section id="about" class="about-section pt-150">
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
                            <img src="/images/site-about-2.png" alt="" class="w-100">
                            <img src="{{ url('/') }}/template/main-site-saaspal-free-lite/assets/img/about/about-right-shape.svg" alt="" class="shape shape-1">
                            <img src="{{ url('/') }}/template/main-site-saaspal-free-lite/assets/img/about/right-dots.svg" alt="" class="shape shape-2">
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- ======== about2-section end ======== -->

        <!-- ======== feature-section start ======== -->
        <section id="why" class="feature-extended-section pt-100">
            <div class="feature-extended-wrapper">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-xxl-5 col-xl-6 col-lg-8 col-md-9">
                            <div class="section-title text-center mb-60">
                                <h2 class="mb-25 wow fadeInUp" data-wow-delay=".2s">
                                    {{ __('messages.pages.siteHome.whySection.title') }}
                                </h2>
                                <p class="wow fadeInUp" data-wow-delay=".4s">
                                    {{ __('messages.pages.siteHome.whySection.text') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        @for ($i=1; $i<=6; $i++)
                            <div class="col-lg-4 col-md-6">
                                <div class="single-feature-extended">
                                    <div class="icon">
                                        <i class="{{ __('messages.pages.siteHome.whySection.col'.$i.'Icon') }}"></i>
                                    </div>
                                    <div class="content">
                                        <h3>{{ __('messages.pages.siteHome.whySection.col'.$i.'Title') }}</h3>
                                        <p>
                                            {{ __('messages.pages.siteHome.whySection.col'.$i.'Text') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
        </section>
        <!-- ======== feature-section end ======== -->

        <!-- ======== pricing-section end ======== -->
        <section id="versions" class="pricing-section pt-120 pb-120">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xxl-5 col-xl-6 col-lg-8 col-md-9">
                        <div class="section-title text-center mb-35">
                            <h2 class="mb-25 wow fadeInUp" data-wow-delay=".2s">
                                {{ __('messages.pages.siteHome.versionSection.title') }}
                            </h2>
                            <p class="wow fadeInUp" data-wow-delay=".4s">
                                {{ __('messages.pages.siteHome.versionSection.text') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="tab-content" id="pills-tabContent">
                    <div class="tab-pane fade show active" id="pills-month" role="tabpanel" aria-labelledby="pills-month-tab">
                        <div class="row justify-content-center">
                            @php use App\Presenters\SubscriptionUpgradePresenter as Presenter; @endphp
                            <div class="col-lg-4 col-md-8 col-sm-10">
                                <div class="single-pricing">
                                    <div class="pricing-header">
                                        <h1 class="price">R$0</h1>
                                        <h3 class="package-name">{{ __('messages.components.Features.labelFreePlan') }}</h3>
                                    </div>
                                    <div class="content">
                                        <ul>
                                            @foreach (Presenter::getFeaturesFreePremium() as $feature)
                                                <li>
                                                    <i class="lni {{ $feature['free'] ? 'lni-checkmark active' : 'lni-close' }}"></i>
                                                    {{ $feature['label'] }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-8 col-sm-10">
                                <div class="single-pricing">
                                    <div class="pricing-header">
                                        <h1 class="price">
                                            {{ sprintf('%s %s / %s', __('messages.currency'), Presenter::getLowestPricePlan(), __('messages.month')) }}
                                        </h1>
                                        <small style="font-size:85%;position: relative;top: -35px;" class="text-muted">{{ __('messages.pages.premium.freeVsPremium.startingFrom') }}</small>
                                        <h3 style="margin-top: -24px;" class="package-name">
                                            {{ __('messages.components.Features.labelPremiumPlan') }}
                                        </h3>
                                    </div>
                                    <div class="content">
                                        <ul>
                                            @foreach (Presenter::getFeaturesFreePremium(false) as $feature)
                                                <li>
                                                    <i class="lni lni-checkmark active"></i>
                                                    {{ $feature['label'] }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-10 col-md-3 text-center">
                        <a href="{{ route('app.register') }}" class="main-btn btn-hover border-btn wow fadeInUp" data-wow-delay=".6s" style="visibility: visible; animation-delay: 0.6s; animation-name: fadeInUp;">
                            {{ __('messages.pages.siteHome.versionSection.btnCta') }}
                        </a>
                    </div>
                </div>
            </div>
        </section>
        <!-- ======== pricing-section end ======== -->

        <!-- ======== testimonial-section start ======== -->
        @php
        /*
        <section id="testimonials" class="testimonial-section">
        <div class="container">
            <div class="section-title text-center">
            <h2 class="mb-30">What our customers says</h2>
            </div>
            <div class="testimonial-active-wrapper">
            <div class="shapes">
                <img src="{{ url('/') }}/template/main-site-saaspal-free-lite/assets/img/testimonial/testimonial-shape.svg" alt="" class="shape shape-1">
                <img src="{{ url('/') }}/template/main-site-saaspal-free-lite/assets/img/testimonial/testimonial-dots.svg" alt="" class="shape shape-2">
            </div>

            <div class="testimonial-active">
                <!-- single testimonial -->
                <div class="single-testimonial">
                <div class="row">
                    <div class="col-xl-5 col-lg-5">
                    <div class="testimonial-img">
                        <img src="{{ url('/') }}/template/main-site-saaspal-free-lite/assets/img/testimonial/testimonial-1.png" alt="">
                        <div class="quote">
                        <i class="lni lni-quotation"></i>
                        </div>
                    </div>
                    </div>

                    <div class="col-xl-6 offset-xl-1 col-lg-6 offset-lg-1">
                    <div class="content-wrapper">
                        <div class="content">
                        <p>
                            Lorem ipsum dolor sit amet, consetetur sadipscing elitr,
                            sed dinonumy eirmod tempor invidunt ut labore et dolore
                            magna aliquyam erat, sed diam voluptua. At vero eos et
                            accusam et justo duo dolores et ea rebum. Stet clita
                            kasd gubergren, no sea takimata sanctus est Lorem.
                        </p>
                        </div>
                        <div class="info">
                        <h4>Jonathon Smith</h4>
                        <p>Developer and Youtuber</p>
                        </div>
                    </div>
                    </div>
                </div>
                </div>

                <!-- single testimonial -->
                <div class="single-testimonial">
                <div class="row">
                    <div class="col-xl-5">
                    <div class="testimonial-img">
                        <img src="{{ url('/') }}/template/main-site-saaspal-free-lite/assets/img/testimonial/testimonial-2.png" alt="">
                        <div class="quote">
                        <i class="lni lni-quotation"></i>
                        </div>
                    </div>
                    </div>

                    <div class="col-xl-6 offset-xl-1">
                    <div class="content-wrapper">
                        <div class="content">
                        <p>
                            Lorem ipsum dolor sit amet, consetetur sadipscing elitr,
                            sed dinonumy eirmod tempor invidunt ut labore et dolore
                            magna aliquyam erat, sed diam voluptua. At vero eos et
                            accusam et justo duo dolores et ea rebum. Stet clita
                            kasd gubergren, no sea takimata sanctus est Lorem.
                        </p>
                        </div>
                        <div class="info">
                        <h4>Gray Simon</h4>
                        <p>UIX Designer and Developer</p>
                        </div>
                    </div>
                    </div>
                </div>
                </div>

                <!-- single testimonial -->
                <div class="single-testimonial">
                <div class="row">
                    <div class="col-xl-5">
                    <div class="testimonial-img">
                        <img src="{{ url('/') }}/template/main-site-saaspal-free-lite/assets/img/testimonial/testimonial-3.png" alt="">
                        <div class="quote">
                        <i class="lni lni-quotation"></i>
                        </div>
                    </div>
                    </div>

                    <div class="col-xl-6 offset-xl-1">
                    <div class="content-wrapper">
                        <div class="content">
                        <p>
                            Lorem ipsum dolor sit amet, consetetur sadipscing elitr,
                            sed dinonumy eirmod tempor invidunt ut labore et dolore
                            magna aliquyam erat, sed diam voluptua. At vero eos et
                            accusam et justo duo dolores et ea rebum. Stet clita
                            kasd gubergren, no sea takimata sanctus est Lorem.
                        </p>
                        </div>
                        <div class="info">
                        <h4>Michel Smith</h4>
                        <p>Traveler and Vloger</p>
                        </div>
                    </div>
                    </div>
                </div>
                </div>
            </div>
            </div>
        </div>
        </section>
        */
        @endphp
        <!-- ======== testimonial-section end ======== -->

        <!-- ======== subscribe-section start ======== -->
        @php
        /*
        <section id="contact" class="subscribe-section pt-120">
        <div class="container">
            <div class="subscribe-wrapper img-bg">
            <div class="row align-items-center">
                <div class="col-xl-6 col-lg-7">
                <div class="section-title mb-15">
                    <h2 class="text-white mb-25">Subscribe Our Newsletter</h2>
                    <p class="text-white pr-5">
                    Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed
                    diam nonumy eirmod tempor
                    </p>
                </div>
                </div>
                <div class="col-xl-6 col-lg-5">
                <form action="#" class="subscribe-form">
                    <input type="email" name="subs-email" id="subs-email" placeholder="Your Email">
                    <button type="submit" class="main-btn btn-hover">
                    Subscribe
                    </button>
                </form>
                </div>
            </div>
            </div>
        </div>
        </section>
        */
        @endphp
        <!-- ======== subscribe-section end ======== -->

        <!-- ======== footer start ======== -->
        <footer class="footer">
            <div class="container">
                <div class="widget-wrapper">
                    <div class="row">
                        <div class="col-xl-4 col-lg-4 col-md-6">
                            <div class="footer-widget">
                                <div class="logo mb-30">
                                    <a href="{{ route('site.home') }}">
                                        <img style="position:relative; left:-50px;" src="/images/site-logo-top-white.png" alt="PG Body Tracker - Logo" />
                                    </a>
                                </div>
                                <p class="desc mb-30 text-white">
                                    {{ __('messages.pages.siteHome.footer.leftText') }}
                                </p>
                                <ul class="socials">
                                    <li>
                                        <a target="_blank" href="https://www.facebook.com/people/PG-Body-Tracker/61577056899425/">
                                            <i class="lni lni-facebook-filled"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <a target="_blank" href="https://www.instagram.com/pgbodytracker">
                                            <i class="lni lni-instagram-filled"></i>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="col-xl-2 col-lg-2 col-md-6">
                            <div class="footer-widget">
                                <h3>{{ __('messages.pages.siteHome.footer.aboutTitle') }}</h3>
                                <ul class="links">
                                    <li>
                                        <a class="page-scroll active" href="#home">
                                            {{ __('messages.pages.siteHome.header.menuHome') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a class="page-scroll" href="#features">
                                            {{ __('messages.pages.siteHome.header.menuFeatures') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a class="page-scroll" href="#about">
                                            {{ __('messages.pages.siteHome.header.menuAbout') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a class="page-scroll" href="#why">
                                            {{ __('messages.pages.siteHome.header.menuWhyUs') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a class="page-scroll" href="#versions">
                                            {{ __('messages.pages.siteHome.header.menuVersions') }}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="col-xl-3 col-lg-3 col-md-6">
                            <div class="footer-widget">
                                <h3>Features</h3>
                                <ul class="links">
                                    <li><a href="javascript:void(0)">How it works</a></li>
                                    <li><a href="javascript:void(0)">Privacy policy</a></li>
                                    <li><a href="javascript:void(0)">Terms of service</a></li>
                                    <li><a href="javascript:void(0)">Refund policy</a></li>
                                </ul>
                            </div>
                        </div>

                        <div class="col-xl-3 col-lg-3 col-md-6">
                            <div class="footer-widget">
                                <h3>Other Products</h3>
                                <ul class="links">
                                    <li><a href="jvascript:void(0)">Accounting Software</a></li>
                                    <li><a href="jvascript:void(0)">Billing Software</a></li>
                                    <li><a href="jvascript:void(0)">Booking System</a></li>
                                    <li><a href="jvascript:void(0)">Tracking System</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!-- ======== footer end ======== -->

        <!-- ======== scroll-top ======== -->
        <a href="#" class="scroll-top btn-hover">
        <i class="lni lni-chevron-up"></i>
        </a>

        <!-- ======== JS here ======== -->
        <script src="{{ url('/') }}/template/main-site-saaspal-free-lite/assets/js/bootstrap.bundle.min.js"></script>
        <script src="{{ url('/') }}/template/main-site-saaspal-free-lite/assets/js/tiny-slider.js"></script>
        <script src="{{ url('/') }}/template/main-site-saaspal-free-lite/assets/js/wow.min.js"></script>
        <script src="{{ url('/') }}/template/main-site-saaspal-free-lite/assets/js/main.js"></script>
    </body>
</html>

