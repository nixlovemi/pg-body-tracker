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
