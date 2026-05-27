@inject('checkinConfigPresenter', 'App\Presenters\CheckinConfigPresenter')

@php
/**
 * View variables:
 *  - $PAGE_TITLE: string
 *  - $DASH_PAGE_TITLE: string
 *  - $CLIENT: App\Models\Client
 *  - $CHECKIN_CONFIG: ?App\Models\CheckinConfig
 *  - $FIELDS_CONFIG: array
 *  - $COPY_SOURCE_CLIENTS: array<int, array{codedId:string,name:string,has_config:bool}>
 */
@endphp

@extends('layout.dashboard', [
    'PAGE_TITLE' => $PAGE_TITLE ?? '',
    'DASH_PAGE_TITLE' => $DASH_PAGE_TITLE ?? '',
])

@section('DASH_BODY_CONTENT')
    @php
        $oldFields = old('f-fields');
        $initialFields = is_array($oldFields) ? $oldFields : ($FIELDS_CONFIG ?? []);
        $checkinConfigHelpContent = __('messages.pages.checkin.config.helpMessage');
        $availableFieldTypes = $checkinConfigPresenter::getAvailableFieldTypes();
        $defaultFieldType = $availableFieldTypes[0]['value'] ?? '';
        $copySourceClients = is_array($COPY_SOURCE_CLIENTS ?? null) ? $COPY_SOURCE_CLIENTS : [];
        $targetHasConfig = $CHECKIN_CONFIG?->id ? true : false;
    @endphp

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                {{ __('messages.pages.checkin.config.cardTitle', ['clientName' => $CLIENT->getName()]) }}
                <x-info-icon-modal
                    title="{{ __('messages.pages.checkin.config.helpTitle') }}"
                    :message="$checkinConfigHelpContent"
                />
            </h6>
            <span class="badge badge-{{ ($CHECKIN_CONFIG?->active ?? false) ? 'success' : 'secondary' }}">
                {{ ($CHECKIN_CONFIG?->active ?? false) ? __('messages.pages.checkin.config.badgeActive') : __('messages.pages.checkin.config.badgeInactive') }}
            </span>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('app.checkin.doSaveConfig') }}">
                @csrf
                <input type="hidden" name="f-cid" value="{{ $CLIENT->codedId }}" />

                <div class="form-row">
                    <div class="form-group col-md-2">
                        <label for="f-interval-days">{{ __('messages.pages.checkin.config.intervalDays') }}</label>
                        @php
                            $intervalDays = (int) old('f-interval-days', $CHECKIN_CONFIG?->interval_days ?? $checkinConfigPresenter::DEFAULT_INTERVAL_DAYS);
                            $intervalDayOptions = $checkinConfigPresenter::getIntervalDayOptions();
                        @endphp
                        <select class="form-control" id="f-interval-days" name="f-interval-days">
                            @if (!in_array($intervalDays, $intervalDayOptions, true))
                                <option value="{{ $intervalDays }}" selected>{{ $intervalDays }}</option>
                            @endif
                            @foreach ($intervalDayOptions as $days)
                                <option value="{{ $days }}" {{ $days === $intervalDays ? 'selected' : '' }}>{{ $days }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-2">
                        <label for="f-link-expires-hours">{{ __('messages.pages.checkin.config.linkExpiresHours') }}</label>
                        @php
                            $linkExpiresHours = (int) old('f-link-expires-hours', $CHECKIN_CONFIG?->link_expires_hours ?? $checkinConfigPresenter::DEFAULT_LINK_EXPIRES_HOURS);
                            $linkExpiresOptions = $checkinConfigPresenter::getLinkExpiresOptions();
                        @endphp
                        <select class="form-control" id="f-link-expires-hours" name="f-link-expires-hours">
                            @if (!in_array($linkExpiresHours, $linkExpiresOptions, true))
                                <option value="{{ $linkExpiresHours }}" selected>{{ $linkExpiresHours }}</option>
                            @endif
                            @foreach ($linkExpiresOptions as $hours)
                                <option value="{{ $hours }}" {{ $hours === $linkExpiresHours ? 'selected' : '' }}>{{ $hours }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-2 d-flex align-items-end">
                        <div class="custom-control custom-switch" style="position:relative; top:-6px;">
                            <input
                                type="checkbox"
                                class="custom-control-input"
                                id="f-active"
                                name="f-active"
                                value="1"
                                {{ old('f-active', ($CHECKIN_CONFIG?->active ?? true) ? '1' : '0') ? 'checked' : '' }}
                            />
                            <label class="custom-control-label" for="f-active">
                                {{ __('messages.pages.checkin.config.active') }}
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>{{ __('messages.pages.checkin.config.fieldsBuilderLabel') }}</label>
                    <small class="form-text text-muted mb-2">
                        {{ __('messages.pages.checkin.config.fieldsBuilderHelp') }}
                    </small>

                    <div id="checkin-fields-builder"></div>

                    <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="btn-add-checkin-field">
                        {{ __('messages.pages.checkin.config.addFieldButton') }}
                    </button>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-primary mr-2">
                        {{ __('messages.buttonSave') }}
                    </button>

                    <a href="{{ route('app.client.edit', ['codedId' => $CLIENT->codedId]) }}" class="btn btn-light border mr-2">
                        {{ __('messages.buttonBack') }}
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('messages.pages.checkin.config.copyTitle') }}</h6>
        </div>
        <div class="card-body">
            <p class="mb-3">{{ __('messages.pages.checkin.config.copyDescription') }}</p>

            @if (count($copySourceClients) === 0)
                <div class="alert alert-light border mb-0">
                    <small class="d-block">{{ __('messages.pages.checkin.config.copyNoSourceAvailable') }}</small>
                </div>
            @else
                <form method="POST" action="{{ route('app.checkin.copyConfigFromClient') }}" id="checkin-copy-config-form">
                    @csrf
                    <input type="hidden" name="f-target-cid" value="{{ $CLIENT->codedId }}" />

                    <div class="form-row align-items-end">
                        <div class="form-group col-md-8">
                            <label for="f-source-cid">{{ __('messages.pages.checkin.config.copySourceLabel') }}</label>
                            <select class="form-control" id="f-source-cid" name="f-source-cid" required>
                                <option value="">{{ __('messages.selectEmptyOption') }}</option>
                                @foreach ($copySourceClients as $source)
                                    <option value="{{ $source['codedId'] }}">{{ $source['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <button type="submit" class="btn btn-outline-primary btn-block">{{ __('messages.pages.checkin.config.copyButton') }}</button>
                        </div>
                    </div>

                    @if ($targetHasConfig)
                        <div class="alert alert-warning mb-0">
                            <small class="d-block">{{ __('messages.pages.checkin.config.copyOverwriteWarning') }}</small>
                        </div>
                    @endif
                </form>
            @endif
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('messages.pages.checkin.config.manualSendTitle') }}</h6>
        </div>
        <div class="card-body">
            <div class="alert alert-light border mb-3">
                <small class="d-block">{{ __('messages.pages.checkin.config.scheduleAutoInfo') }}</small>
            </div>

            <p class="mb-3">{{ __('messages.pages.checkin.config.manualSendDescription') }}</p>

            <form
                method="POST"
                action="{{ route('app.checkin.sendNow', ['clientCodedId' => $CLIENT->codedId]) }}"
                id="checkin-manual-send-form"
                data-client-email="{{ (string) ($CLIENT->email ?? '') }}"
            >
                @csrf
                <button type="submit" class="btn btn-outline-primary">
                    {{ __('messages.pages.checkin.config.sendNowButton') }}
                </button>
            </form>
        </div>
    </div>

    <script>
        (function () {
            var initialFields = @json($initialFields);
            var i18n = {
                fieldLabel: @json(__('messages.pages.checkin.config.fieldLabel')),
                fieldType: @json(__('messages.pages.checkin.config.fieldType')),
                fieldRequired: @json(__('messages.pages.checkin.config.fieldRequired')),
                optionsLabel: @json(__('messages.pages.checkin.config.optionsLabel')),
                optionPlaceholder: @json(__('messages.pages.checkin.config.optionPlaceholder')),
                addOption: @json(__('messages.pages.checkin.config.addOptionButton')),
                removeOption: @json(__('messages.pages.checkin.config.removeOptionButton')),
                removeField: @json(__('messages.pages.checkin.config.removeFieldButton')),
                noFields: @json(__('messages.pages.checkin.config.noFieldsYet')),
            };

            var fieldTypeOptions = @json($availableFieldTypes);
            var defaultFieldTypeValue = @json((string) $defaultFieldType);
            var copyWillOverwriteCurrentConfig = @json($targetHasConfig);
            var copyConfirmTitle = @json(__('messages.jsAlertConfirmTitle'));
            var copyOverwriteConfirmMsg = @json(__('messages.pages.checkin.config.copyOverwriteConfirm'));
            var copyConfirmYes = @json(__('messages.jsAlertConfirmYes'));
            var copyConfirmClose = @json(__('messages.buttonClose'));
            var manualSendConfirmMsg = @json(__('messages.pages.checkin.config.manualSendConfirm'));
            var manualSendNoEmailTitle = @json(__('messages.jsAlertInfoTitle'));
            var manualSendNoEmailMsg = @json(__('messages.pages.checkin.config.manualSendMissingEmail'));
            var supportsOptionsByType = {};
            fieldTypeOptions.forEach(function (option) {
                supportsOptionsByType[String(option.value || '')] = !!option.supports_options;
            });

            var builder = document.getElementById('checkin-fields-builder');
            var addFieldBtn = document.getElementById('btn-add-checkin-field');
            var uid = 0;

            function defaultFieldType() {
                return defaultFieldTypeValue;
            }

            function getFieldTypeOptionsHtml() {
                return fieldTypeOptions.map(function (option) {
                    return '<option value="' + String(option.value || '') + '">' + String(option.label || option.value || '') + '</option>';
                }).join('');
            }

            function slugify(value) {
                return String(value || '')
                    .toLowerCase()
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .replace(/[^a-z0-9]+/g, '_')
                    .replace(/^_+|_+$/g, '')
                    .substring(0, 60);
            }

            function normalizeOptions(rawOptions) {
                if (Array.isArray(rawOptions)) {
                    return rawOptions.filter(Boolean).map(function (value) {
                        return String(value);
                    });
                }

                if (rawOptions && typeof rawOptions === 'object') {
                    return Object.keys(rawOptions).map(function (key) {
                        return String(rawOptions[key] || '').trim();
                    }).filter(Boolean);
                }

                return [];
            }

            function createOptionRow(fieldUid, value) {
                var wrapper = document.createElement('div');
                wrapper.className = 'input-group mb-2 js-option-row';

                var input = document.createElement('input');
                input.type = 'text';
                input.className = 'form-control js-option-input';
                input.placeholder = i18n.optionPlaceholder;
                input.value = value || '';
                input.name = 'f-fields[' + fieldUid + '][options][]';

                var append = document.createElement('div');
                append.className = 'input-group-append';

                var remove = document.createElement('button');
                remove.type = 'button';
                remove.className = 'btn btn-light border text-danger';
                remove.textContent = i18n.removeOption;
                remove.addEventListener('click', function () {
                    wrapper.remove();
                });

                append.appendChild(remove);
                wrapper.appendChild(input);
                wrapper.appendChild(append);

                return wrapper;
            }

            function addField(fieldData) {
                uid += 1;
                var fieldUid = uid;
                var data = fieldData || {};
                var fieldType = String(data.field_type || defaultFieldType());
                var fieldLabel = String(data.label || '');
                var fieldKey = String(data.field_key || '');

                var card = document.createElement('div');
                card.className = 'card border mb-3';
                card.dataset.fieldUid = String(fieldUid);

                card.innerHTML =
                    '<div class="card-body">' +
                        '<div class="form-row">' +
                            '<div class="form-group col-md-5">' +
                                '<label>' + i18n.fieldLabel + '</label>' +
                                '<input type="text" class="form-control js-field-label" name="f-fields[' + fieldUid + '][label]" maxlength="120" />' +
                            '</div>' +
                            '<div class="form-group col-md-3">' +
                                '<label>' + i18n.fieldType + '</label>' +
                                '<select class="form-control js-field-type" name="f-fields[' + fieldUid + '][field_type]">' +
                                    getFieldTypeOptionsHtml() +
                                '</select>' +
                            '</div>' +
                            '<div class="form-group col-md-2">' +
                                '<label>' + i18n.fieldRequired + '</label>' +
                                '<input type="hidden" name="f-fields[' + fieldUid + '][required]" value="0" />' +
                                '<div class="custom-control custom-switch mt-2">' +
                                    '<input type="checkbox" class="custom-control-input js-field-required" id="f-field-required-' + fieldUid + '" name="f-fields[' + fieldUid + '][required]" value="1" />' +
                                    '<label class="custom-control-label" for="f-field-required-' + fieldUid + '"></label>' +
                                '</div>' +
                            '</div>' +
                            '<div class="form-group col-md-2 d-flex align-items-end">' +
                                '<button type="button" class="btn btn-light border text-danger btn-block js-remove-field">' + i18n.removeField + '</button>' +
                            '</div>' +
                        '</div>' +
                        '<input type="hidden" class="js-field-key" name="f-fields[' + fieldUid + '][field_key]" />' +
                        '<div class="js-options-wrap">' +
                            '<label>' + i18n.optionsLabel + '</label>' +
                            '<div class="js-options-list"></div>' +
                            '<button type="button" class="btn btn-sm btn-outline-secondary js-add-option">' + i18n.addOption + '</button>' +
                        '</div>' +
                    '</div>';

                var labelInput = card.querySelector('.js-field-label');
                var typeSelect = card.querySelector('.js-field-type');
                var requiredInput = card.querySelector('.js-field-required');
                var keyInput = card.querySelector('.js-field-key');
                var optionsWrap = card.querySelector('.js-options-wrap');
                var optionsList = card.querySelector('.js-options-list');
                var addOptionBtn = card.querySelector('.js-add-option');
                var removeFieldBtn = card.querySelector('.js-remove-field');

                labelInput.value = fieldLabel;
                var availableTypeValues = fieldTypeOptions.map(function (option) { return String(option.value || ''); });
                typeSelect.value = availableTypeValues.indexOf(fieldType) >= 0 ? fieldType : defaultFieldType();
                requiredInput.checked = String(data.required || '0') === '1' || data.required === true || data.required === 1;

                var generatedKey = fieldKey || slugify(fieldLabel) || ('field_' + fieldUid);
                keyInput.value = generatedKey;

                labelInput.addEventListener('input', function () {
                    var nextKey = slugify(labelInput.value) || ('field_' + fieldUid);
                    keyInput.value = nextKey;
                });

                addOptionBtn.addEventListener('click', function () {
                    optionsList.appendChild(createOptionRow(fieldUid, ''));
                });

                removeFieldBtn.addEventListener('click', function () {
                    card.remove();
                    renderNoFieldsHint();
                });

                typeSelect.addEventListener('change', function () {
                    var showOptions = !!supportsOptionsByType[typeSelect.value];
                    optionsWrap.style.display = showOptions ? '' : 'none';
                    Array.prototype.forEach.call(optionsList.querySelectorAll('input'), function (optionInput) {
                        optionInput.disabled = !showOptions;
                    });
                });

                var options = normalizeOptions(data.options);
                if (options.length === 0) {
                    options = [''];
                }

                options.forEach(function (optionValue) {
                    optionsList.appendChild(createOptionRow(fieldUid, optionValue));
                });

                builder.appendChild(card);
                typeSelect.dispatchEvent(new Event('change'));
                renderNoFieldsHint();
            }

            function renderNoFieldsHint() {
                var existingHint = builder.querySelector('.js-no-fields-hint');
                var hasCards = builder.querySelectorAll('.card').length > 0;

                if (!hasCards && !existingHint) {
                    var hint = document.createElement('div');
                    hint.className = 'alert alert-light border js-no-fields-hint';
                    hint.textContent = i18n.noFields;
                    builder.appendChild(hint);
                }

                if (hasCards && existingHint) {
                    existingHint.remove();
                }
            }

            addFieldBtn.addEventListener('click', function () {
                addField({ field_type: defaultFieldType(), label: '', required: false, options: [''] });
            });

            if (Array.isArray(initialFields) && initialFields.length > 0) {
                initialFields.forEach(function (field) {
                    addField(field || {});
                });
            } else {
                addField({ field_type: defaultFieldType(), label: '', required: true, options: [''] });
            }

            var copyForm = document.getElementById('checkin-copy-config-form');
            if (copyForm) {
                copyForm.addEventListener('submit', function (event) {
                    if (copyForm.dataset.confirmed === '1') {
                        copyForm.dataset.confirmed = '0';
                        return;
                    }

                    if (!copyWillOverwriteCurrentConfig) {
                        return;
                    }

                    event.preventDefault();

                    // Keep a safe fallback when SweetAlert is unavailable.
                    if (typeof Swal === 'undefined') {
                        if (window.confirm(copyOverwriteConfirmMsg)) {
                            copyForm.dataset.confirmed = '1';
                            copyForm.submit();
                        }
                        return;
                    }

                    Swal.fire({
                        title: copyConfirmTitle,
                        html: copyOverwriteConfirmMsg,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: copyConfirmYes,
                        cancelButtonText: copyConfirmClose,
                    }).then(function (result) {
                        if (!result.isConfirmed) {
                            return;
                        }

                        copyForm.dataset.confirmed = '1';
                        copyForm.submit();
                    });
                });
            }

            var manualSendForm = document.getElementById('checkin-manual-send-form');
            if (manualSendForm) {
                manualSendForm.addEventListener('submit', function (event) {
                    if (manualSendForm.dataset.confirmed === '1') {
                        manualSendForm.dataset.confirmed = '0';
                        return;
                    }

                    var clientEmail = String(manualSendForm.dataset.clientEmail || '').trim();
                    if (clientEmail === '') {
                        event.preventDefault();

                        if (typeof Swal === 'undefined') {
                            window.alert(manualSendNoEmailMsg);
                            return;
                        }

                        Swal.fire({
                            icon: 'info',
                            title: manualSendNoEmailTitle,
                            html: manualSendNoEmailMsg,
                        });

                        return;
                    }

                    event.preventDefault();

                    if (typeof Swal === 'undefined') {
                        if (window.confirm(manualSendConfirmMsg)) {
                            manualSendForm.dataset.confirmed = '1';
                            manualSendForm.submit();
                        }
                        return;
                    }

                    Swal.fire({
                        title: copyConfirmTitle,
                        html: manualSendConfirmMsg,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: copyConfirmYes,
                        cancelButtonText: copyConfirmClose,
                    }).then(function (result) {
                        if (!result.isConfirmed) {
                            return;
                        }

                        manualSendForm.dataset.confirmed = '1';
                        manualSendForm.submit();
                    });
                });
            }
        })();
    </script>
@endsection
