@inject('Presenter', 'App\Presenters\SubscriptionUpgradePresenter')
@php $selName = 'f-subscriptionType'; @endphp

<select
    class="form-control form-control-user"
    id="{{ $selName }}"
    name="{{ $selName }}"
>
    @foreach ($Presenter::getPlans() as $planKey => $planInfo)
        @php
            $selValue = old($selName, $Presenter::getDefaultPlanKey());
        @endphp

        <option data-url="{{ route('app.subscription.subscribe', ['plan' => $planKey]) }}" value="{{ $planKey }}" {{ $planKey === $selValue ? 'selected' : '' }}>
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

<script>
    document.getElementById('{{$selName}}').addEventListener('change', function() {
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
    document.getElementById('{{$selName}}').dispatchEvent(new Event('change'));
</script>
