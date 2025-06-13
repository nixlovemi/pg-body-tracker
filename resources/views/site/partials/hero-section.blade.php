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
                <img src="/images/site-hero-top.webp" alt="Dashboard do sistema PG BodyTracker exibindo avaliações físicas de clientes">
                </div>
            </div>
        </div>
    </div>
</section>
<!-- ======== hero-section end ======== -->
