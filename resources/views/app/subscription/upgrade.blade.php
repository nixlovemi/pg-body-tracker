@inject('Presenter', 'App\Presenters\SubscriptionUpgradePresenter')

@extends('layout.dashboard', [
    'PAGE_TITLE' => $PAGE_TITLE ?? ''
])

@section('DASH_BODY_CONTENT')
    <div class="row">
        <div class="col">
            <x-card title="{{__('messages.pages.premium.subscribe')}}">
                <div class="container-fluid">
                    <h1 class="h3 mb-4 text-gray-800 text-center">
                        {{ __('messages.pages.premium.upgradeTitle') }}
                    </h1>

                    <p class="text-center mb-4">
                        {!! __('messages.pages.premium.upgradeDescription') !!}
                    </p>

                    <div class="alert alert-info border-left-primary mb-4" role="alert">
                        <h5 class="alert-heading mb-2">
                            {{ __('messages.pages.premium.checkinHighlight.title') }}
                        </h5>
                        <p class="mb-1">
                            {{ __('messages.pages.premium.checkinHighlight.description') }}
                        </p>
                        <small class="text-muted">
                            {{ __('messages.pages.premium.checkinHighlight.note') }}
                        </small>
                    </div>

                    <div class="row justify-content-center">
                        <div class="col-12 col-lg-5 mb-4">
                            <div class="card border-left-secondary shadow text-center h-100">
                                <div class="card-body">
                                    <h4 class="text-secondary font-weight-bold">{{ __('messages.pages.premium.freeVsPremium.freePlan') }}</h4>
                                    <h5 class="text-muted mb-3">{{ sprintf('%s %s / %s', __('messages.currency'), 0, __('messages.month')) }}</h5>
                                    <ul class="list-unstyled text-left">
                                        @foreach ($Presenter::getFeaturesFreePremium() as $feature)
                                            <li>
                                                {!! $feature['free'] ? $Presenter::getIconTrue() : $Presenter::getIconFalse() !!}
                                                {{ $feature['label'] }}
                                            </li>
                                        @endforeach
                                    </ul>
                                    <button class="btn btn-outline-secondary mt-3" disabled>
                                        {{ __('messages.pages.premium.freeVsPremium.currentPlan') }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-lg-5 mb-4">
                            <div class="card border-left-primary shadow text-center h-100">
                                <div class="card-body">
                                    <h4 class="text-primary font-weight-bold">{{ __('messages.pages.premium.freeVsPremium.premiumPlan') }}</h4>
                                    <h5 class="text-dark mb-3">{{ sprintf('%s %s %s / %s', __('messages.pages.premium.freeVsPremium.startingFrom'), __('messages.currency'), $Presenter::getLowestPricePlan(), __('messages.month')) }}</h5>
                                    <ul class="list-unstyled text-left">
                                        @foreach ($Presenter::getFeaturesFreePremium(false) as $feature)
                                            <li>
                                                {!! $Presenter::getIconTrue() !!}
                                                {{ $feature['label'] }}
                                            </li>
                                        @endforeach
                                    </ul>
                                    <a id="subscription-upgrade-submit" href="javascript:;" class="btn btn-primary mt-3 mb-3">
                                        {{ __('messages.pages.premium.freeVsPremium.subscribeNow') }}
                                    </a>

                                    @include('app.subscription.partials.premium-select')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </x-card>
        </div>
    </div>

    <script>
        document.getElementById('subscription-upgrade-submit').addEventListener('click', function() {
            const selectedPlan = document.getElementById('f-subscriptionType').value;
            const planUrl = document.querySelector(`#f-subscriptionType option[value="${selectedPlan}"]`).dataset.url;

            // Redirect to the subscription URL for the selected plan
            window.location.href = planUrl;
        });
    </script>
@endsection
