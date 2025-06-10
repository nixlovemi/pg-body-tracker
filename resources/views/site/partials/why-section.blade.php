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
