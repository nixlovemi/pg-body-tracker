
@php
/*
View variables:
===============
    - $AVALIATION: App\Models\Avaliation
===============
*/
@endphp

@extends('layout.modal', [
    'divId' => 'avaliation-modal-send-mail' . date('YmdHis') . rand(),
    'maxHeight' => '100vh',
    'maxWidth' => '800px'
])

@section('MODAL_HEADER')
    <h5 class="modal-title">
        {{ __('messages.pages.avaliation.modalSendWhats.title', [
            'type' => __('messages.pages.client.table.colEmail')
        ]) }}
    </h5>
@endsection

@section('MODAL_BODY')
    <form id="frm-modal-send-mail">
        @csrf
        <input type="hidden" name="cid" value="{{ $AVALIATION?->codedId }}" />

        <p>
            {{ __('messages.pages.avaliation.modalSendWhats.message') }}
        </p>

        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <label class="form-label" title="{{ __('messages.pages.client.table.colEmail') }}">
                        {{ __('messages.pages.client.table.colEmail') }}
                    </label>
                    <input type="text" class="form-control form-control-user"
                        id="email" name="email" maxlength="200"
                        value="{{ $AVALIATION?->client->email ?? '' }}"
                    />
                </div>
            </div>
        </div>

        <div class="form-actions">
            <div class="float-right">
                <button type="submit" class="btn-modal-submit btn btn-sm primary btn-user">
                    {{  __('messages.pages.avaliation.modalSendWhats.buttonSend') }}
                </button>

                <a href="javascript:;" class="btn-modal-close btn btn-sm btn-light" data-dismiss="modal">
                    {{  __('messages.buttonClose') }}
                </a>
            </div>
        </div>
    </form>
@endsection
