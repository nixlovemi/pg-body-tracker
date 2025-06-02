@inject('AppDashboardPresenter', 'App\Presenters\AppDashboardPresenter')

@php
/*
View variables:
===============
    - $PAGE_TITLE: string
*/
@endphp

@extends('layout.dashboard', [
    'PAGE_TITLE' => $PAGE_TITLE ?? ''
])

@section('DASH_BODY_CONTENT')
    <div class="row">
        @foreach ($AppDashboardPresenter::getDashboardCardData() as $cardClass)
            @php
                $card = new $cardClass();
            @endphp
            <div class="col-12 col-lg-4 col-xl-3 mb-3">
                <x-dashboard-card
                    :card="$card"
                />
            </div>
        @endforeach
    </div>
@endsection
