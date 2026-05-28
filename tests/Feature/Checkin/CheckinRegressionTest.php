<?php

namespace Tests\Feature\Checkin;

use App\Enums\CheckinFieldType;
use App\Helpers\ApiResponse;
use App\Helpers\Feature\FeatureAbstract;
use App\Helpers\CheckinFields\Fields\YesNoField;
use App\Helpers\CheckinFields\Fields\WeightField;
use App\Helpers\SysUtils;
use App\Models\Avaliation;
use App\Models\AvaliationCheckinField;
use App\Models\CheckinConfig;
use App\Models\Client;
use App\Models\User;
use App\Models\UserPlans;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CheckinRegressionTest extends TestCase
{
    use DatabaseTransactions;

    public function testAvaliationSaveAcceptsNullRevaluationDate(): void
    {
        [$user, $client] = $this->createManagerAndClient();
        $this->actingAs($user, 'web');

        $response = Avaliation::fSave([
            'client_id' => $client->id,
            'date' => '2026-05-27',
            'weight_kg' => 82.3,
            'calculate_perc_fat_by' => Avaliation::CALCULATE_PERC_FAT_BY_BIOIMPEDANCE,
            'revaluation_date' => null,
        ]);

        $this->assertInstanceOf(ApiResponse::class, $response);
        $this->assertFalse($response->isError(), $response->getMessage());

        /** @var Avaliation|null $avaliation */
        $avaliation = $response->getValueFromResponse('Avaliation');
        $this->assertNotNull($avaliation);
        $this->assertNull($avaliation->revaluation_date);
    }

    public function testCreatingAvaliationUpdatesNextCheckinDateWhenConfigIsActive(): void
    {
        [$user, $client] = $this->createManagerAndClient();
        $this->actingAs($user, 'web');

        $config = CheckinConfig::create([
            'client_id' => $client->id,
            'active' => true,
            'interval_days' => 9,
            'link_expires_hours' => 24,
        ]);

        $response = Avaliation::fSave([
            'client_id' => $client->id,
            'date' => '2026-05-10',
            'weight_kg' => 80.0,
            'calculate_perc_fat_by' => Avaliation::CALCULATE_PERC_FAT_BY_BIOIMPEDANCE,
        ]);

        $this->assertFalse($response->isError(), $response->getMessage());

        $config->refresh();
        $this->assertSame('2026-05-19', optional($config->next_checkin_date)->format('Y-m-d'));
    }

    public function testUpdatingExistingAvaliationDoesNotRescheduleNextCheckinDate(): void
    {
        [$user, $client] = $this->createManagerAndClient();
        $this->actingAs($user, 'web');

        $config = CheckinConfig::create([
            'client_id' => $client->id,
            'active' => true,
            'interval_days' => 7,
            'link_expires_hours' => 24,
            'next_checkin_date' => '2026-06-01',
        ]);

        $createResponse = Avaliation::fSave([
            'client_id' => $client->id,
            'date' => '2026-05-10',
            'weight_kg' => 80.0,
            'calculate_perc_fat_by' => Avaliation::CALCULATE_PERC_FAT_BY_BIOIMPEDANCE,
        ]);

        $this->assertFalse($createResponse->isError(), $createResponse->getMessage());

        /** @var Avaliation|null $avaliation */
        $avaliation = $createResponse->getValueFromResponse('Avaliation');
        $this->assertNotNull($avaliation);

        $config->refresh();
        $scheduledOnCreate = optional($config->next_checkin_date)->format('Y-m-d');

        $updateResponse = Avaliation::fSave([
            'client_id' => $client->id,
            'date' => $avaliation->date,
            'weight_kg' => 81.2,
            'calculate_perc_fat_by' => $avaliation->calculate_perc_fat_by,
        ], $avaliation->codedId);

        $this->assertFalse($updateResponse->isError(), $updateResponse->getMessage());

        $config->refresh();
        $this->assertSame($scheduledOnCreate, optional($config->next_checkin_date)->format('Y-m-d'));
    }

    public function testFollowupSubmitIsAcceptedOnceAndBlocksSecondSubmission(): void
    {
        [$user, $client] = $this->createManagerAndClient();
        $this->actingAs($user, 'web');

        $config = CheckinConfig::create([
            'client_id' => $client->id,
            'active' => true,
            'interval_days' => 7,
            'link_expires_hours' => 24,
            'fields_config' => [],
        ]);

        [$formUrl, $submitUrl, $formSignature, $formExpires] = $this->buildSignedFollowupUrls($config);

        $this->get($formUrl)
            ->assertOk()
            ->assertSee(__('messages.pages.checkin.followup.formTitle'));

        $submitPayload = [
            'f-weight' => '82,3',
            'f-form-link-signature' => $formSignature,
            'f-form-link-expires' => $formExpires,
        ];

        $this->post($submitUrl, $submitPayload)
            ->assertOk()
            ->assertSee(__('messages.pages.checkin.followup.thankYouTitle'));

        $today = SysUtils::timezoneNow('Y-m-d');
        $avaliation = Avaliation::query()
            ->where('client_id', $client->id)
            ->where('date', $today)
            ->first();

        $this->assertNotNull($avaliation);
        $this->assertDatabaseHas('avaliation_checkin_fields', [
            'avaliation_id' => $avaliation->id,
            'field_key' => 'weight_kg',
        ]);
        $this->assertSame(1, Avaliation::query()->where('client_id', $client->id)->where('date', $today)->count());
        $this->assertSame(1, AvaliationCheckinField::query()->where('avaliation_id', $avaliation->id)->count());

        $this->post($submitUrl, $submitPayload)
            ->assertOk()
            ->assertSee(__('messages.pages.checkin.followup.alreadySubmittedTitle'));

        $this->assertSame(1, Avaliation::query()->where('client_id', $client->id)->where('date', $today)->count());
        $this->assertSame(1, AvaliationCheckinField::query()->where('avaliation_id', $avaliation->id)->count());
    }

    public function testFollowupFormAfterSubmitShowsAlreadySubmittedState(): void
    {
        [$user, $client] = $this->createManagerAndClient();
        $this->actingAs($user, 'web');

        $config = CheckinConfig::create([
            'client_id' => $client->id,
            'active' => true,
            'interval_days' => 7,
            'link_expires_hours' => 24,
            'fields_config' => [],
        ]);

        [$formUrl, $submitUrl, $formSignature, $formExpires] = $this->buildSignedFollowupUrls($config);

        $this->post($submitUrl, [
            'f-weight' => '79,8',
            'f-form-link-signature' => $formSignature,
            'f-form-link-expires' => $formExpires,
        ])
            ->assertOk()
            ->assertSee(__('messages.pages.checkin.followup.thankYouTitle'));

        $this->get($formUrl)
            ->assertOk()
            ->assertSee(__('messages.pages.checkin.followup.alreadySubmittedTitle'))
            ->assertSee($client->getName());
    }

    public function testFollowupFormRendersYesNoQuestionWithTranslatedOptions(): void
    {
        [$user, $client] = $this->createManagerAndClient();
        $this->actingAs($user, 'web');

        $config = CheckinConfig::create([
            'client_id' => $client->id,
            'active' => true,
            'interval_days' => 7,
            'link_expires_hours' => 24,
            'fields_config' => [
                [
                    'field_type' => CheckinFieldType::YES_NO,
                    'field_key' => 'followed_plan',
                    'label' => 'Did you follow the plan?',
                    'required' => true,
                    'options' => ['ignored' => 'ignored'],
                ],
            ],
        ]);

        [$formUrl] = $this->buildSignedFollowupUrls($config);

        $this->get($formUrl)
            ->assertOk()
            ->assertSee('Did you follow the plan?')
            ->assertSee(__('messages.yes'))
            ->assertSee(__('messages.no'));
    }

    public function testFollowupSubmitPersistsYesNoResponseAsBooleanType(): void
    {
        [$user, $client] = $this->createManagerAndClient();
        $this->actingAs($user, 'web');

        $config = CheckinConfig::create([
            'client_id' => $client->id,
            'active' => true,
            'interval_days' => 7,
            'link_expires_hours' => 24,
            'fields_config' => [
                [
                    'field_type' => CheckinFieldType::YES_NO,
                    'field_key' => 'followed_plan',
                    'label' => 'Did you follow the plan?',
                    'required' => true,
                    'options' => ['x' => 'x'],
                ],
            ],
        ]);

        [, $submitUrl, $formSignature, $formExpires] = $this->buildSignedFollowupUrls($config);

        $this->post($submitUrl, [
            'f-weight' => '81,0',
            'f-followed_plan' => YesNoField::VALUE_YES,
            'f-form-link-signature' => $formSignature,
            'f-form-link-expires' => $formExpires,
        ])
            ->assertOk()
            ->assertSee(__('messages.pages.checkin.followup.thankYouTitle'));

        $today = SysUtils::timezoneNow('Y-m-d');
        $avaliation = Avaliation::query()
            ->where('client_id', $client->id)
            ->where('date', $today)
            ->first();

        $this->assertNotNull($avaliation);

        $this->assertDatabaseHas('avaliation_checkin_fields', [
            'avaliation_id' => $avaliation->id,
            'field_type' => CheckinFieldType::YES_NO,
            'field_key' => 'followed_plan',
            'response' => YesNoField::VALUE_YES,
            'response_type' => AvaliationCheckinField::RESPONSE_TYPE_BOOLEAN,
        ]);

        $saved = AvaliationCheckinField::query()
            ->where('avaliation_id', $avaliation->id)
            ->where('field_key', 'followed_plan')
            ->first();

        $this->assertNotNull($saved);
        $this->assertSame([], (array) $saved->field_meta['options'] ?? []);
    }

    public function testClientSummarySeparatesLastSentAndLastRespondedDates(): void
    {
        [$user, $client] = $this->createManagerAndClient();
        $this->actingAs($user, 'web');

        CheckinConfig::create([
            'client_id' => $client->id,
            'active' => true,
            'interval_days' => 7,
            'link_expires_hours' => 24,
            'next_checkin_date' => '2026-05-28',
            'last_checkin_sent_date' => '2026-05-21',
        ]);

        $summaryBeforeResponse = $client->fresh()->getCheckinSummary();
        $this->assertSame('21/05/2026', $summaryBeforeResponse['last_checkin_sent_date']);
        $this->assertNull($summaryBeforeResponse['last_checkin_responded_date']);

        $saveResponse = Avaliation::fSave([
            'client_id' => $client->id,
            'date' => '2026-05-24',
            'weight_kg' => 81.4,
            'calculate_perc_fat_by' => Avaliation::CALCULATE_PERC_FAT_BY_BIOIMPEDANCE,
        ]);
        $this->assertFalse($saveResponse->isError(), $saveResponse->getMessage());

        /** @var Avaliation|null $avaliation */
        $avaliation = $saveResponse->getValueFromResponse('Avaliation');
        $this->assertNotNull($avaliation);

        AvaliationCheckinField::create([
            'avaliation_id' => $avaliation->id,
            'field_class' => WeightField::class,
            'field_type' => CheckinFieldType::WEIGHT,
            'field_key' => 'weight_kg',
            'response' => '81.4',
            'response_type' => AvaliationCheckinField::RESPONSE_TYPE_NUMBER,
            'field_meta' => [],
        ]);

        $summaryAfterResponse = $client->fresh()->getCheckinSummary();
        $this->assertSame('21/05/2026', $summaryAfterResponse['last_checkin_sent_date']);
        $this->assertSame('24/05/2026', $summaryAfterResponse['last_checkin_responded_date']);
    }

    public function testCopyConfigFromAnotherClientOverwritesConfigAndPreservesOperationalDates(): void
    {
        [$user, $targetClient] = $this->createManagerAndClient();
        $this->withoutMiddleware();
        $this->grantPremiumPlan($user);
        $this->actingAs($user, 'web');

        $sourceClient = Client::factory()->create([
            'user_id' => $user->id,
            'gender' => Client::GENDER_FEMALE,
            'birthdate' => '1994-03-10',
            'height_cm' => 168,
            'weight_kg' => 72,
        ]);

        CheckinConfig::create([
            'client_id' => $sourceClient->id,
            'active' => false,
            'interval_days' => 14,
            'link_expires_hours' => 48,
            'fields_config' => [
                [
                    'field_type' => CheckinFieldType::YES_NO,
                    'field_key' => 'followed_plan',
                    'label' => 'Did you follow the plan?',
                    'required' => true,
                    'options' => [],
                ],
            ],
            'next_checkin_date' => '2026-07-01',
            'last_checkin_date' => '2026-06-01',
            'last_checkin_sent_date' => '2026-06-01',
        ]);

        $targetConfig = CheckinConfig::create([
            'client_id' => $targetClient->id,
            'active' => true,
            'interval_days' => 7,
            'link_expires_hours' => 24,
            'fields_config' => [
                [
                    'field_type' => CheckinFieldType::TEXTAREA,
                    'field_key' => 'notes',
                    'label' => 'messages.pages.checkin.fields.notes.label',
                    'required' => false,
                    'options' => [],
                ],
            ],
            'next_checkin_date' => '2026-06-15',
            'last_checkin_date' => '2026-06-02',
            'last_checkin_sent_date' => '2026-06-03',
        ]);

        $this->post(route('app.checkin.copyConfigFromClient'), [
            'f-target-cid' => $targetClient->codedId,
            'f-source-cid' => $sourceClient->codedId,
        ])
            ->assertRedirect(route('app.checkin.config', ['clientCodedId' => $targetClient->codedId]))
            ->assertSessionHas('success');

        $targetConfig->refresh();

        $this->assertFalse($targetConfig->active);
        $this->assertSame(14, (int) $targetConfig->interval_days);
        $this->assertSame(48, (int) $targetConfig->link_expires_hours);
        $this->assertSame('2026-06-15', optional($targetConfig->next_checkin_date)->format('Y-m-d'));
        $this->assertSame('2026-06-02', optional($targetConfig->last_checkin_date)->format('Y-m-d'));
        $this->assertSame('2026-06-03', optional($targetConfig->last_checkin_sent_date)->format('Y-m-d'));

        $copiedFields = $targetConfig->getFieldsConfig();
        $this->assertCount(1, $copiedFields);
        $this->assertSame(CheckinFieldType::YES_NO, (string) ($copiedFields[0]['field_type'] ?? ''));
        $this->assertSame('followed_plan', (string) ($copiedFields[0]['field_key'] ?? ''));
    }

    public function testCopyConfigFromAnotherClientRejectsSourceWithoutConfig(): void
    {
        [$user, $targetClient] = $this->createManagerAndClient();
        $this->withoutMiddleware();
        $this->grantPremiumPlan($user);
        $this->actingAs($user, 'web');

        CheckinConfig::create([
            'client_id' => $targetClient->id,
            'active' => true,
            'interval_days' => 7,
            'link_expires_hours' => 24,
            'fields_config' => [],
        ]);

        $sourceClient = Client::factory()->create([
            'user_id' => $user->id,
            'gender' => Client::GENDER_FEMALE,
            'birthdate' => '1994-03-10',
            'height_cm' => 168,
            'weight_kg' => 72,
        ]);

        $this->post(route('app.checkin.copyConfigFromClient'), [
            'f-target-cid' => $targetClient->codedId,
            'f-source-cid' => $sourceClient->codedId,
        ])
            ->assertRedirect(route('app.checkin.config', ['clientCodedId' => $targetClient->codedId]))
            ->assertSessionHasErrors('msg');
    }

    private function buildSignedFollowupUrls(CheckinConfig $config): array
    {
        $formUrl = URL::temporarySignedRoute(
            'app.checkin.followup.form',
            now()->addHours((int) $config->link_expires_hours),
            ['configCodedId' => $config->codedId]
        );

        $submitUrl = URL::temporarySignedRoute(
            'app.checkin.followup.submit',
            now()->addHours((int) $config->link_expires_hours),
            ['configCodedId' => $config->codedId]
        );

        $query = parse_url($formUrl, PHP_URL_QUERY) ?: '';
        parse_str($query, $queryParams);

        return [
            $formUrl,
            $submitUrl,
            (string) ($queryParams['signature'] ?? ''),
            (string) ($queryParams['expires'] ?? ''),
        ];
    }

    private function createManagerAndClient(): array
    {
        $user = User::factory()->create([
            'role' => User::ROLE_MANAGER,
            'active' => true,
            'confirmation' => true,
        ]);

        $client = Client::factory()->create([
            'user_id' => $user->id,
            'gender' => Client::GENDER_MALE,
            'birthdate' => '1996-05-27',
            'height_cm' => 175,
            'weight_kg' => 80,
        ]);

        return [$user, $client];
    }

    private function grantPremiumPlan(User $user): void
    {
        UserPlans::create([
            'user_id' => $user->id,
            'plan_type' => FeatureAbstract::FEATURE_PLAN_TYPE_PREMIUM,
            'start_date' => now()->subDay()->format('Y-m-d'),
            'end_date' => now()->addDays(30)->format('Y-m-d'),
            'status' => UserPlans::STATUS_ACTIVE,
            'payment_data' => json_encode(['test' => true]),
        ]);

        Cache::forget($user->getPlanTypeCacheKey());
        $user->refresh();
    }
}
