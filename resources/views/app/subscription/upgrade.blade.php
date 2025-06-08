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

                                    <select
                                        class="form-control form-control-user"
                                        id="f-subscriptionType"
                                        name="f-subscriptionType"
                                    >
                                        @foreach ($Presenter::getPlans() as $planKey => $planInfo)
                                            <option data-url="{{ route('app.subscription.subscribe', ['plan' => $planKey]) }}" value="{{ $planKey }}" {{ $planKey === $Presenter::getDefaultPlanKey() ? 'selected' : '' }}>
                                                {{ $planInfo['label'] }} - {{ sprintf('%s %s / %s*', __('messages.currency'), $planInfo['formatted_price_month'], __('messages.month')) }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @foreach ($Presenter::getPlans() as $planKey => $planInfo)
                                        <small class="d-none mt-2" id="{{ $planKey }}">
                                            {{ __('messages.pages.premium.labelTotalPricePerFrequency', [
                                                'total' => sprintf('%s %s', __('messages.currency'), $planInfo['formatted_price']),
                                                'frequency' => $planInfo['frequency']
                                            ]) }}
                                        </small>
                                    @endforeach
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

        document.getElementById('f-subscriptionType').addEventListener('change', function() {
            const selectedPlan = this.value;
            const plans = @json($Presenter::getPlans());

            // Hide all small elements
            Object.keys(plans).forEach(planKey => {
                document.getElementById(planKey).classList.add('d-none');
            });

            // Show the selected plan's small element
            document.getElementById(selectedPlan).classList.remove('d-none');
        });

        // Trigger change event on page load to show the default plan's price
        document.getElementById('f-subscriptionType').dispatchEvent(new Event('change'));
    </script>
@endsection
