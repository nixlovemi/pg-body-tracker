@inject('mClient', 'App\Models\Client')

@php
/*
View variables:
    - $CUID: string (Client Coded ID)
    - $BEFORE_DEADLINE: string|null (Y-m-d)
===============
*/

$BEFORE_DEADLINE = $BEFORE_DEADLINE ?? null;
@endphp

@extends('layout.modal', [
    'divId' => date('YmdHis') . rand(),
    'maxHeight' => '100vh',
    'maxWidth' => '1000px'
])

@section('MODAL_HEADER')
    <h5 class="modal-title">
        {{ __('messages.pages.client.register.btnOldGoals') }}
    </h5>
@endsection

@section('MODAL_BODY')
    <div id="dv-modal-past-goals">
        @include('app.goal.partials.bodyListClientPastGoals', [
            'CUID' => $CUID,
            'BEFORE_DEADLINE' => $BEFORE_DEADLINE
        ])
    </div>
@endsection
