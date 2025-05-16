
@php
/*
View variables:
===============
    - $AVALIATION: App\Models\Avaliation
===============
*/
@endphp

@extends('layout.modal', [
    'divId' => 'avaliation-modal-send-whats' . date('YmdHis') . rand(),
    'maxHeight' => '100vh',
    'maxWidth' => '800px'
])

@section('MODAL_HEADER')
    <h5 class="modal-title">
        {{ __('messages.pages.avaliation.modalSendWhats.title', [
            'type' => __('messages.components.avaliationReport.sendWhatsLink')
        ]) }}
    </h5>
@endsection

@section('MODAL_BODY')
    <form id="frm-modal-send-whats">
        @csrf
        <input type="hidden" name="cid" value="{{ $AVALIATION?->codedId }}" />

        <p>
            {{ __('messages.pages.avaliation.modalSendWhats.message') }}
        </p>

        <div class="row">
            <div class="col-3">
                <div class="form-group">
                    <label class="form-label" title="{{ __('messages.pages.avaliation.modalSendWhats.fieldCode') }}">
                        {{ __('messages.pages.avaliation.modalSendWhats.fieldCode') }}
                    </label>
                    <x-country-code-select />
                </div>
            </div>
            <div class="col-9">
                <div class="form-group">
                    <label class="form-label" title="{{ __('messages.models.Client.fields.phone') }}">
                        {{ __('messages.models.Client.fields.phone') }}
                    </label>
                    <input type="text" class="form-control form-control-user"
                        id="phone" name="phone" maxlength="15"
                        value="{{ $AVALIATION?->client->phone ?? '' }}"
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
