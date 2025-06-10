@inject('SysUtils', 'App\Helpers\SysUtils')

@php
/*
View variables:
===============
    - $PAGE_TITLE: string
*/

$User = $SysUtils->getLoggedInUser();
@endphp

@extends('layout.dashboard', [
    'PAGE_TITLE' => $PAGE_TITLE ?? ''
])

@section('DASH_BODY_CONTENT')
    <h4>{{ $PAGE_TITLE }}</h4>

    <div class="row">
        <div class="col">
            <form method="POST" action="{{ route('app.support.doSend') }}" class="form-horizontal">
                @csrf

                <x-card title="{{ __('messages.pages.support.cardTitle') }}">
                    <p>{{ __('messages.pages.support.cardDescription') }}</p>

                    <div class="form-row">
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label class="form-label" title="{{ __('messages.models.User.fields.name') }}">
                                    * {{ __('messages.models.User.fields.name') }}
                                </label>
                                <input type="text" class="form-control form-control-user"
                                    id="contact-name" name="contact-name" maxlength="60"
                                    value="{{ old('contact-name') ?: $User?->getFullName() }}"
                                    required readonly
                                />
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label class="form-label" title="{{ __('messages.pages.client.table.colEmail') }}">
                                    * {{ __('messages.pages.client.table.colEmail') }}
                                </label>
                                <input type="email" class="form-control form-control-user"
                                    id="contact-email" name="contact-email" maxlength="255"
                                    value="{{ old('contact-email') ?: $User?->email }}"
                                    pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                                    required readonly
                                />
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label" title="{{ __('messages.pages.support.contactSubject') }}">
                                    * {{ __('messages.pages.support.contactSubject') }}
                                </label>
                                <select class="form-control form-control-user"
                                    id="contact-subject" name="contact-subject" required
                                >
                                    <option value="general" {{ old('contact-subject') == 'general' ? 'selected' : '' }}>
                                        {{ __('messages.pages.support.subject.general') }}
                                    </option>
                                    <option value="billing" {{ old('contact-subject') == 'billing' ? 'selected' : '' }}>
                                        {{ __('messages.pages.support.subject.billing') }}
                                    </option>
                                    <option value="technical" {{ old('contact-subject') == 'technical' ? 'selected' : '' }}>
                                        {{ __('messages.pages.support.subject.technical') }}
                                    </option>
                                    <option value="feedback" {{ old('contact-subject') == 'feedback' ? 'selected' : '' }}>
                                        {{ __('messages.pages.support.subject.feedback') }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label" title="{{ __('messages.pages.support.contactMessage') }}">
                                    * {{ __('messages.pages.support.contactMessage') }}
                                </label>
                                <textarea class="form-control form-control-user"
                                    id="contact-message" name="contact-message" rows="5"
                                    maxlength="2000"
                                    required>{{ old('contact-message') }}</textarea>
                            </div>
                        </div>
                    </div>
                </x-card>

                <div class="form-actions">
                    <div class="text-right">
                        <button type="submit" class="btn primary btn-user">{{ __('messages.pages.login.forgot.sendButton') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
