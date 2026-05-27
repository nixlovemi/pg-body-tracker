<?php

namespace App\Http\Controllers;

use App\DTO\Checkin\CheckinFieldConfigDTO;
use App\Enums\CheckinFieldType;
use App\Helpers\ApiResponse;
use App\Helpers\CheckinFields\CheckinFieldRegistry;
use App\Helpers\CheckinFields\Fields\WeightField;
use App\Helpers\LocalLogger;
use App\Helpers\SysUtils;
use App\Mail\CheckinSubmittedNotification;
use App\Mail\SendCheckinFollowupLink;
use App\Models\Avaliation as mAvaliation;
use App\Models\AvaliationCheckinField;
use App\Models\CheckinConfig;
use App\Models\Client;
use App\Models\UrlShort;
use App\Models\User;
use App\Services\Checkin\CheckinFieldConfigNormalizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class Checkin extends Controller
{
    private const CHECKIN_ALREADY_SUBMITTED_CODE = '__CHECKIN_ALREADY_SUBMITTED__';

    public function config(string $clientCodedId)
    {
        $Client = Client::getModelByCodedId($clientCodedId);
        if (!$Client || !Client::fHasAccess($Client)) {
            return redirect()
                ->route('app.client.index')
                ->withErrors(['msg' => __('messages.saveModelNotFound', ['modelName' => __('messages.models.Client.name')])]);
        }

        $CheckinConfig = $Client->checkinConfig;
        $fieldsConfig = $CheckinConfig?->getFieldsConfig();
        if (empty($fieldsConfig) || !is_array($fieldsConfig)) {
            $fieldsConfig = $this->getDefaultFieldsConfig();
        }

        $fieldsConfig = $this->localizeFieldsConfigForForm($fieldsConfig);

        return view('app.checkin.config', [
            'PAGE_TITLE' => __('messages.pages.checkin.config.title'),
            'DASH_PAGE_TITLE' => __('messages.pages.checkin.config.title'),
            'CLIENT' => $Client,
            'CHECKIN_CONFIG' => $CheckinConfig,
            'FIELDS_CONFIG' => $fieldsConfig,
            'COPY_SOURCE_CLIENTS' => $this->getCopySourceClients($Client),
        ]);
    }

    public function doSaveConfig(Request $request): RedirectResponse
    {
        $clientCodedId = (string) $request->input('f-cid', '');
        $Client = Client::getModelByCodedId($clientCodedId);
        if (!$Client || !Client::fHasAccess($Client)) {
            return redirect()
                ->route('app.client.index')
                ->withErrors(['msg' => __('messages.saveModelNotFound', ['modelName' => __('messages.models.Client.name')])]);
        }

        $rawFields = $request->input('f-fields', []);
        if (!is_array($rawFields)) {
            $decodedFields = json_decode((string) $request->input('f-fields-config', '[]'), true);
            $rawFields = is_array($decodedFields) ? $decodedFields : [];
        }

        $normalizedFields = app(CheckinFieldConfigNormalizer::class)->normalize($rawFields);
        if (count($normalizedFields) === 0) {
            return $this->redirectConfigError($Client, __('messages.pages.checkin.config.emptyFieldsConfig'));
        }

        $nextCheckinDate = $Client->checkinConfig?->next_checkin_date?->format('Y-m-d');

        $codedId = $Client->checkinConfig?->codedId;
        $form = [
            'client_id' => $Client->id,
            'active' => $request->boolean('f-active'),
            'interval_days' => (int) $request->input('f-interval-days', 7),
            'link_expires_hours' => (int) $request->input('f-link-expires-hours', 24),
            'fields_config' => $normalizedFields,
            'next_checkin_date' => $nextCheckinDate,
        ];

        $ret = CheckinConfig::fSave($form, $codedId);
        if ($ret->isError()) {
            return $this->redirectConfigError($Client, ApiResponse::getValidateMessage($ret));
        }

        return redirect()
            ->route('app.checkin.config', ['clientCodedId' => $Client->codedId])
            ->withSuccess(__('messages.pages.checkin.config.saveSuccess'));
    }

    public function copyConfigFromClient(Request $request): RedirectResponse
    {
        $targetClientCodedId = (string) $request->input('f-target-cid', '');
        $sourceClientCodedId = (string) $request->input('f-source-cid', '');

        $TargetClient = Client::getModelByCodedId($targetClientCodedId);
        if (!$TargetClient || !Client::fHasAccess($TargetClient)) {
            return redirect()
                ->route('app.client.index')
                ->withErrors(['msg' => __('messages.saveModelNotFound', ['modelName' => __('messages.models.Client.name')])]);
        }

        $SourceClient = Client::getModelByCodedId($sourceClientCodedId);
        if (!$SourceClient || !Client::fHasAccess($SourceClient)) {
            return $this->redirectConfigError($TargetClient, __('messages.pages.checkin.config.copySourceNotFound'));
        }

        if ($SourceClient->id === $TargetClient->id) {
            return $this->redirectConfigError($TargetClient, __('messages.pages.checkin.config.copySourceSameClient'));
        }

        $sourceConfig = $SourceClient->checkinConfig;
        if (!$sourceConfig) {
            return $this->redirectConfigError($TargetClient, __('messages.pages.checkin.config.copySourceHasNoConfig'));
        }

        $targetConfig = $TargetClient->checkinConfig;
        $targetHasConfig = $targetConfig !== null;

        $fieldsConfig = app(CheckinFieldConfigNormalizer::class)->normalize((array) $sourceConfig->getFieldsConfig());
        if (count($fieldsConfig) === 0) {
            return $this->redirectConfigError($TargetClient, __('messages.pages.checkin.config.emptyFieldsConfig'));
        }

        $ret = CheckinConfig::fSave([
            'client_id' => $TargetClient->id,
            'active' => (bool) $sourceConfig->active,
            'interval_days' => (int) $sourceConfig->interval_days,
            'link_expires_hours' => (int) $sourceConfig->link_expires_hours,
            'fields_config' => $fieldsConfig,
            // Preserve scheduling state from target; do not copy operational dates.
            'next_checkin_date' => $targetConfig?->next_checkin_date?->format('Y-m-d'),
        ], $targetConfig?->codedId);

        if ($ret->isError()) {
            return $this->redirectConfigError($TargetClient, ApiResponse::getValidateMessage($ret));
        }

        $successMsg = $targetHasConfig
            ? __('messages.pages.checkin.config.copyOverwriteSuccess', ['clientName' => $SourceClient->getName()])
            : __('messages.pages.checkin.config.copySuccess', ['clientName' => $SourceClient->getName()]);

        return redirect()
            ->route('app.checkin.config', ['clientCodedId' => $TargetClient->codedId])
            ->withSuccess($successMsg);
    }

    public function sendNow(string $clientCodedId): RedirectResponse
    {
        $Client = Client::getModelByCodedId($clientCodedId);
        if (!$Client || !Client::fHasAccess($Client)) {
            return redirect()
                ->route('app.client.index')
                ->withErrors(['msg' => __('messages.saveModelNotFound', ['modelName' => __('messages.models.Client.name')])]);
        }

        $CheckinConfig = $Client->checkinConfig;
        if (!$CheckinConfig) {
            return $this->redirectConfigError($Client, __('messages.pages.checkin.config.notConfiguredYet'));
        }

        if (!$CheckinConfig->active) {
            return $this->redirectConfigError($Client, __('messages.pages.checkin.config.mustBeActiveToSend'));
        }

        if (!filter_var($Client->email, FILTER_VALIDATE_EMAIL)) {
            return $this->redirectConfigError($Client, __('messages.pages.login.forgot.errorMailNotValid'));
        }

        $portalLink = $this->buildSignedFollowupLink($CheckinConfig);
        $shortUrl = UrlShort::make($portalLink);

        Mail::to($Client->email)
            ->send(new SendCheckinFollowupLink($Client, $shortUrl));

        $todayYmd = SysUtils::timezoneNow('Y-m-d');
        $nowTz = now()->setTimezone(env('APP_TIME_ZONE'));
        $CheckinConfig->last_checkin_sent_date = $todayYmd;
        $CheckinConfig->last_checkin_sent_at = $nowTz;
        $CheckinConfig->unanswered_reminders_sent = 0;
        $CheckinConfig->next_checkin_date = SysUtils::applyTimezone($todayYmd)
            ->addDays((int) $CheckinConfig->interval_days)
            ->format('Y-m-d');
        $CheckinConfig->save();

        return redirect()
            ->route('app.checkin.config', ['clientCodedId' => $Client->codedId])
            ->withSuccess(__('messages.pages.checkin.config.sendSuccess'));
    }

    /** signed route */
    public function followupForm(Request $request, string $configCodedId)
    {
        $CheckinConfig = CheckinConfig::getModelByCodedId($configCodedId);
        if (!$CheckinConfig || !$CheckinConfig->active || !$CheckinConfig->client) {
            return view('app.signed-expired');
        }

        if ($this->hasSubmittedCheckinForCurrentEvaluation($CheckinConfig->client)) {
            return $this->renderAlreadySubmittedView($CheckinConfig->client);
        }

        if ($this->isFollowupFormLinkConsumed($request, $configCodedId)) {
            return $this->renderAlreadySubmittedView($CheckinConfig->client);
        }

        $fieldsConfig = app(CheckinFieldConfigNormalizer::class)->normalize((array) $CheckinConfig->getFieldsConfig());
        $submitUrl = URL::temporarySignedRoute(
            'app.checkin.followup.submit',
            now()->addHours((int) $CheckinConfig->link_expires_hours),
            ['configCodedId' => $CheckinConfig->codedId]
        );

        return view('app.checkin.followup-form', [
            'PAGE_TITLE' => __('messages.pages.checkin.followup.title'),
            'CHECKIN_CONFIG' => $CheckinConfig,
            'CLIENT' => $CheckinConfig->client,
            'FIELDS' => $fieldsConfig,
            'SUBMIT_URL' => $submitUrl,
            'FORM_LINK_SIGNATURE' => (string) $request->query('signature', ''),
            'FORM_LINK_EXPIRES' => (string) $request->query('expires', ''),
        ]);
    }

    /** signed route */
    public function followupSubmit(Request $request, string $configCodedId)
    {
        $CheckinConfig = CheckinConfig::getModelByCodedId($configCodedId);
        if (!$CheckinConfig || !$CheckinConfig->active || !$CheckinConfig->client) {
            return view('app.signed-expired');
        }

        if ($this->hasSubmittedCheckinForCurrentEvaluation($CheckinConfig->client)) {
            return $this->renderAlreadySubmittedView($CheckinConfig->client);
        }

        if ($this->isFollowupSubmitLinkConsumed($request, $configCodedId)) {
            return $this->renderAlreadySubmittedView($CheckinConfig->client);
        }

        $Client = $CheckinConfig->client;
        $fieldsConfig = app(CheckinFieldConfigNormalizer::class)->normalize((array) $CheckinConfig->getFieldsConfig());

        $validationErrors = [];
        $responses = [];

        $weightField = CheckinFieldRegistry::make(CheckinFieldType::WEIGHT, ['field_key' => WeightField::FIELD_KEY]);
        if (!$weightField) {
            return $this->redirectPublicError(__('messages.pages.checkin.followup.unexpectedFieldError'));
        }

        $rawWeight = $request->input('f-weight');
        $weightValidate = $weightField->validateResponse($rawWeight);
        if ($weightValidate->isError()) {
            $validationErrors[] = $weightValidate->getMessage();
        } else {
            $normalizedWeight = $weightField->normalizeResponse($rawWeight);
            $responses[] = [
                'field_class' => get_class($weightField),
                'field_type' => $weightField->getFieldType(),
                'field_key' => $weightField->getFieldKey(),
                'response' => (string) $normalizedWeight,
                'response_type' => $weightField->getResponseType(),
                'field_meta' => $weightField->getFieldMeta(),
                'normalized_value' => $normalizedWeight,
                'field_instance' => $weightField,
            ];
        }

        foreach ($fieldsConfig as $fieldConfig) {
            $fieldType = (string) ($fieldConfig['field_type'] ?? '');
            $fieldKey = (string) ($fieldConfig['field_key'] ?? '');
            if ($fieldType === '' || $fieldKey === '') {
                continue;
            }

            $Field = CheckinFieldRegistry::make($fieldType, $fieldConfig);
            if (!$Field) {
                continue;
            }

            $inputName = 'f-' . $fieldKey;
            $rawValue = $request->input($inputName);
            $required = (bool) ($fieldConfig['required'] ?? false);
            if ($required && (is_null($rawValue) || trim((string) $rawValue) === '')) {
                $validationErrors[] = __('messages.pages.checkin.followup.fieldRequired', ['field' => $fieldKey]);
                continue;
            }

            $validate = $Field->validateResponse($rawValue);
            if ($validate->isError()) {
                $validationErrors[] = $validate->getMessage();
                continue;
            }

            $normalized = $Field->normalizeResponse($rawValue);
            $responses[] = [
                'field_class' => get_class($Field),
                'field_type' => $Field->getFieldType(),
                'field_key' => $Field->getFieldKey(),
                'response' => is_null($normalized) ? null : (string) $normalized,
                'response_type' => $Field->getResponseType(),
                'field_meta' => $Field->getFieldMeta(),
                'normalized_value' => $normalized,
                'field_instance' => $Field,
            ];
        }

        if (!empty($validationErrors)) {
            return redirect()->back()->withInput()->withErrors($validationErrors);
        }

        $result = $this->executeAsClientOwner((int) $Client->user_id, function () use ($Client, $CheckinConfig, $responses) {
            $today = SysUtils::timezoneNow('Y-m-d');
            $todayLabel = SysUtils::reformatDate($today, 'Y-m-d', __('messages.dateFormat'));

            $Avaliation = mAvaliation::where('client_id', $Client->id)
                ->where('date', $today)
                ->first();

            $isNewAvaliation = false;

            if (!$Avaliation) {
                $Avaliation = new mAvaliation();
                $Avaliation->client_id = $Client->id;
                $Avaliation->date = $today;
                $isNewAvaliation = true;
            }

            $Avaliation->age = $Client->getAge();
            $Avaliation->height_cm = (int) $Client->height_cm;
            $Avaliation->calculate_perc_fat_by = mAvaliation::CALCULATE_PERC_FAT_BY_MEASURES;

            foreach ($responses as $row) {
                $row['field_instance']->applyToAvaliation($Avaliation, $row['normalized_value']);
            }

            // Safety check: do not allow a second check-in for the same evaluation.
            if ($Avaliation->exists && $Avaliation->checkinFields()->exists()) {
                return new ApiResponse(true, self::CHECKIN_ALREADY_SUBMITTED_CODE);
            }

            if ($isNewAvaliation && trim((string) $Avaliation->private_notes) === '') {
                $Avaliation->private_notes = __('messages.pages.checkin.followup.generatedAvaliationPrivateNote', [
                    'date' => $todayLabel,
                ]);
            }

            $validate = $Avaliation->validateModel();
            if ($validate->isError()) {
                return $validate;
            }

            $Avaliation->save();
            $Avaliation->refresh();

            foreach ($responses as $row) {
                AvaliationCheckinField::updateOrCreate(
                    [
                        'avaliation_id' => $Avaliation->id,
                        'field_key' => $row['field_key'],
                    ],
                    [
                        'field_class' => $row['field_class'],
                        'field_type' => $row['field_type'],
                        'response' => $row['response'],
                        'response_type' => $row['response_type'],
                        'field_meta' => $row['field_meta'],
                    ]
                );
            }

            $CheckinConfig->last_checkin_date = $today;
            $CheckinConfig->unanswered_reminders_sent = 0;
            $CheckinConfig->next_checkin_date = now()
                ->setTimezone(env('APP_TIME_ZONE'))
                ->addDays((int) $CheckinConfig->interval_days)
                ->format('Y-m-d');
            $CheckinConfig->save();

            return new ApiResponse(false, 'ok', ['Avaliation' => $Avaliation]);
        });

        if ($result->isError()) {
            if ($result->getMessage() === self::CHECKIN_ALREADY_SUBMITTED_CODE) {
                return $this->renderAlreadySubmittedView($Client);
            }

            return $this->redirectPublicError(ApiResponse::getValidateMessage($result));
        }

        $this->markFollowupLinkAsConsumed($request, $configCodedId);

        try {
            if ($Client->user?->email) {
                Mail::to($Client->user->email)
                    ->send(new CheckinSubmittedNotification($Client, $result->getValueFromResponse('Avaliation')));
            }
        } catch (\Throwable $th) {
            LocalLogger::log('checkin_submitted_notification_error', ['message' => $th->getMessage()]);
        }

        return view('app.checkin.followup-success', [
            'PAGE_TITLE' => __('messages.pages.checkin.followup.thankYouTitle'),
            'CLIENT' => $Client,
        ]);
    }

    private function executeAsClientOwner(int $ownerUserId, callable $callback): ApiResponse
    {
        $currentUser = SysUtils::getLoggedInUser();
        $switched = false;

        try {
            if (!$currentUser || $currentUser->id !== $ownerUserId) {
                if (!SysUtils::loginUserTempById($ownerUserId, 5)) {
                    return new ApiResponse(true, __('messages.pages.checkin.followup.unableToPersist'));
                }
                $switched = true;
            }

            $result = $callback();
            if ($result instanceof ApiResponse) {
                return $result;
            }

            return new ApiResponse(false, 'ok');
        } catch (\Throwable $th) {
            LocalLogger::log('checkin_submit_error', ['message' => $th->getMessage()]);
            return new ApiResponse(true, __('messages.pages.checkin.followup.unableToPersist'));
        } finally {
            if ($switched) {
                if ($currentUser instanceof User) {
                    SysUtils::loginUser($currentUser);
                } else {
                    SysUtils::logout(false);
                }
            }
        }
    }

    private function buildSignedFollowupLink(CheckinConfig $CheckinConfig): string
    {
        return URL::temporarySignedRoute(
            'app.checkin.followup.form',
            now()->addHours((int) $CheckinConfig->link_expires_hours),
            ['configCodedId' => $CheckinConfig->codedId]
        );
    }

    private function isFollowupFormLinkConsumed(Request $request, string $configCodedId): bool
    {
        $signature = (string) $request->query('signature', '');
        $expires = (string) $request->query('expires', '');
        $cacheKey = $this->buildConsumedFollowupLinkCacheKey($configCodedId, $signature, $expires);

        return $cacheKey !== null && Cache::has($cacheKey);
    }

    private function isFollowupSubmitLinkConsumed(Request $request, string $configCodedId): bool
    {
        $signature = (string) $request->input('f-form-link-signature', '');
        $expires = (string) $request->input('f-form-link-expires', '');
        $cacheKey = $this->buildConsumedFollowupLinkCacheKey($configCodedId, $signature, $expires);

        return $cacheKey !== null && Cache::has($cacheKey);
    }

    private function markFollowupLinkAsConsumed(Request $request, string $configCodedId): void
    {
        $signature = (string) $request->input('f-form-link-signature', '');
        $expires = (string) $request->input('f-form-link-expires', '');
        $cacheKey = $this->buildConsumedFollowupLinkCacheKey($configCodedId, $signature, $expires);
        if ($cacheKey === null) {
            return;
        }

        $expiresTs = ctype_digit($expires) ? (int) $expires : 0;
        $secondsLeft = $expiresTs - now()->timestamp;
        if ($secondsLeft <= 0) {
            $secondsLeft = 60;
        }

        Cache::put($cacheKey, true, now()->addSeconds($secondsLeft));
    }

    private function buildConsumedFollowupLinkCacheKey(string $configCodedId, string $signature, string $expires): ?string
    {
        if ($configCodedId === '' || $signature === '' || $expires === '') {
            return null;
        }

        return 'checkin:consumed_link:' . sha1($configCodedId . '|' . $expires . '|' . $signature);
    }

    private function hasSubmittedCheckinForCurrentEvaluation(Client $Client): bool
    {
        $today = SysUtils::timezoneNow('Y-m-d');

        return mAvaliation::where('client_id', $Client->id)
            ->where('date', $today)
            ->whereHas('checkinFields')
            ->exists();
    }

    private function renderAlreadySubmittedView(Client $Client)
    {
        return view('app.checkin.followup-success', [
            'PAGE_TITLE' => __('messages.pages.checkin.followup.alreadySubmittedTitle'),
            'CLIENT' => $Client,
            'SUCCESS_TITLE' => __('messages.pages.checkin.followup.alreadySubmittedTitle'),
            'SUCCESS_DESCRIPTION' => __('messages.pages.checkin.followup.alreadySubmittedDescription', ['clientName' => $Client->getName()]),
        ]);
    }

    /**
     * @param array<int, array<string, mixed>> $fieldsConfig
     * @return array<int, array<string, mixed>>
     */
    private function localizeFieldsConfigForForm(array $fieldsConfig): array
    {
        foreach ($fieldsConfig as $index => $field) {
            if (!is_array($field)) {
                continue;
            }

            $label = (string) ($field['label'] ?? '');
            if ($this->looksLikeMessageKey($label)) {
                $fieldsConfig[$index]['label'] = $this->translateFieldMessageKey($label);
            }

            $options = $field['options'] ?? null;
            if (!is_array($options)) {
                continue;
            }

            foreach ($options as $optKey => $optValue) {
                if (!is_string($optValue)) {
                    continue;
                }

                if ($this->looksLikeMessageKey($optValue)) {
                    $options[$optKey] = $this->translateFieldMessageKey($optValue);
                }
            }

            $fieldsConfig[$index]['options'] = $options;
        }

        return $fieldsConfig;
    }

    private function looksLikeMessageKey(string $value): bool
    {
        return Str::startsWith($value, 'messages.');
    }

    private function translateFieldMessageKey(string $messageKey): string
    {
        if (Lang::has($messageKey)) {
            return __($messageKey);
        }

        if (Str::startsWith($messageKey, 'messages.checkin.')) {
            $fallbackKey = Str::replaceFirst('messages.checkin.', 'messages.pages.checkin.', $messageKey);
            if (Lang::has($fallbackKey)) {
                return __($fallbackKey);
            }
        }

        return $messageKey;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function getDefaultFieldsConfig(): array
    {
        return [
            (new CheckinFieldConfigDTO())
                ->setFieldType(CheckinFieldType::SELECT)
                ->setFieldKey('how_was_diet')
                ->setLabel('messages.pages.checkin.fields.how_was_diet.label')
                ->setRequired(true)
                ->setOptions([
                    'great' => 'messages.pages.checkin.fields.how_was_diet.options.great',
                    'good' => 'messages.pages.checkin.fields.how_was_diet.options.good',
                    'ok' => 'messages.pages.checkin.fields.how_was_diet.options.ok',
                    'bad' => 'messages.pages.checkin.fields.how_was_diet.options.bad',
                ])
                ->toArray(),
            (new CheckinFieldConfigDTO())
                ->setFieldType(CheckinFieldType::TEXTAREA)
                ->setFieldKey('notes')
                ->setLabel('messages.pages.checkin.fields.notes.label')
                ->setRequired(false)
                ->setOptions([])
                ->toArray(),
        ];
    }

    /**
     * @return array<int, array{codedId:string,name:string,has_config:bool}>
     */
    private function getCopySourceClients(Client $targetClient): array
    {
        return Client::query()
            ->where('user_id', $targetClient->user_id)
            ->where('id', '!=', $targetClient->id)
            ->whereHas('checkinConfig')
            ->with('checkinConfig')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get()
            ->map(fn(Client $client) => [
                'codedId' => (string) $client->codedId,
                'name' => $client->getName(),
                'has_config' => $client->checkinConfig !== null,
            ])
            ->values()
            ->all();
    }

    private function redirectConfigError(Client $Client, string $message): RedirectResponse
    {
        return redirect()
            ->route('app.checkin.config', ['clientCodedId' => $Client->codedId])
            ->withInput()
            ->withErrors(['msg' => $message]);
    }

    private function redirectPublicError(string $message): RedirectResponse
    {
        return redirect()
            ->back()
            ->withInput()
            ->withErrors(['msg' => $message]);
    }
}
