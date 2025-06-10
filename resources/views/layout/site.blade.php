@php use App\Presenters\LayoutSitePresenter; @endphp

<!DOCTYPE html>
<html dir="ltr" lang="pt-BR">
    <head>
        <title>{{ $PAGE_TITLE ?? '' }} | {{ env('SITE_DISPLAY_NAME') }}</title>
        <meta charset="utf-8" />
        <meta http-equiv="x-ua-compatible" content="ie=edge" />
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

        @yield('SITE_HEADER_CUSTOM_CSS')
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
                                        @foreach (LayoutSitePresenter::getMenuLinks(Route::currentRouteName()) as $item)
                                            <li class="nav-item">
                                                <a class="page-scroll {{ $loop->index == 0 ? 'active' : '' }}" href="{{ $item['url'] }}">
                                                    {{ $item['title'] }}
                                                </a>
                                            </li>
                                        @endforeach
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

        @yield('SITE_BODY_CONTENT')

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

                        <div class="col-xl-3 col-lg-3 col-md-6">
                            <div class="footer-widget">
                                @php
                                /*just to fake the last commented block*/
                                @endphp
                            </div>
                        </div>

                        <div class="col-xl-2 col-lg-2 col-md-6">
                            <div class="footer-widget">
                                <h3>{{ __('messages.pages.siteHome.footer.aboutTitle') }}</h3>
                                <ul class="links">
                                    @foreach (LayoutSitePresenter::getMenuLinks(Route::currentRouteName()) as $item)
                                        <li>
                                            <a class="page-scroll {{ $loop->index == 0 ? 'active' : '' }}" href="{{ $item['url'] }}">
                                                {{ $item['title'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        <div class="col-xl-3 col-lg-3 col-md-6">
                            <div class="footer-widget">
                                <h3>{{ __('messages.pages.siteHome.footer.featuresTitle') }}</h3>
                                <ul class="links">
                                    <li>
                                        <a href="{{ route('site.privacy') }}">
                                            {{ __('messages.pages.sitePrivacy.title') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('site.terms') }}">
                                            {{ __('messages.pages.siteTerms.title') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('site.faq') }}">
                                            {{ __('messages.pages.siteFaq.title') }}
                                        </a>
                                    </li>
                                    @php
                                    /*<li><a href="javascript:void(0)">Refund policy</a></li>*/
                                    @endphp
                                </ul>
                            </div>
                        </div>

                        @php
                        /*
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
                        */
                        @endphp
                    </div>
                </div>
            </div>
        </footer>
        <!-- ======== footer end ======== -->

        <!-- ======== scroll-top ======== -->
        <a href="#home" class="scroll-top btn-hover">
            <i class="lni lni-chevron-up"></i>
        </a>

        <!-- ======== JS here ======== -->
        <script src="{{ url('/') }}/template/main-site-saaspal-free-lite/assets/js/bootstrap.bundle.min.js"></script>
        <script src="{{ url('/') }}/template/main-site-saaspal-free-lite/assets/js/tiny-slider.js"></script>
        <script src="{{ url('/') }}/template/main-site-saaspal-free-lite/assets/js/wow.min.js"></script>
        <script src="{{ url('/') }}/template/main-site-saaspal-free-lite/assets/js/main.js"></script>
        @yield('SITE_BODY_CUSTOM_JS')
    </body>
</html>
