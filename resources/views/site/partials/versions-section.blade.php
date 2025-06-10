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
