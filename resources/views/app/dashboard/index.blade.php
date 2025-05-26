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
        @php
        $cards = [
            'App\Helpers\DashboardCard\DashCardMonthAvaliations',
            'App\Helpers\DashboardCard\DashCardMonthClients',
            'App\Helpers\DashboardCard\DashCardClientsWithoutAvaliation30Days',
            'App\Helpers\DashboardCard\DashCardClientsWithGoalsDueThisWeek',
        ];
        @endphp

        @foreach ($cards as $cardClass)
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
