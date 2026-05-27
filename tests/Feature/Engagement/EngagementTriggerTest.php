<?php

namespace Tests\Feature\Engagement;

use App\Jobs\SendEngagementDigestEmailJob;
use App\Models\Avaliation;
use App\Models\Client;
use App\Models\User;
use App\Models\UserEngagement;
use App\Models\UserPlans;
use App\Helpers\Feature\FeatureAbstract;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class EngagementTriggerTest extends TestCase
{
    use RefreshDatabase;

    public function testDispatchCommandQueuesDigestJobAndStoresState()
    {
        Queue::fake();

        User::factory()->create([
            'role' => User::ROLE_MANAGER,
            'active' => true,
            'confirmation' => true,
            'last_login_at' => now()->subDays(10),
        ]);

        $this->artisan('engagement:dispatch')
            ->expectsOutput('Engagement dispatch finished.')
            ->assertSuccessful();

        Queue::assertPushed(SendEngagementDigestEmailJob::class, 1);

        $this->assertDatabaseCount('user_engagements', 1);

        $engagement = UserEngagement::query()->first();
        $this->assertNotNull($engagement?->last_sent_at);
        $this->assertEquals('digest', $engagement?->last_sent_type);
        $this->assertNotEmpty($engagement?->last_payload);
    }

    public function testDispatchCommandSkipsDisabledAlertPreferences()
    {
        Queue::fake();

        /** @var User $user */
        $user = User::factory()->create([
            'role' => User::ROLE_MANAGER,
            'active' => true,
            'confirmation' => true,
            'last_login_at' => now()->subDays(10),
        ]);

        UserEngagement::create([
            'user_id' => $user->id,
            'opt_out' => false,
            'alert_preferences' => [
                UserEngagement::ALERT_INACTIVE_LOGIN => false,
                UserEngagement::ALERT_MISSING_SETUP => false,
                UserEngagement::ALERT_BIRTHDAY_TODAY => false,
                UserEngagement::ALERT_GOAL_NEAR_DEADLINE => false,
                UserEngagement::ALERT_CLIENT_WITHOUT_RECENT_AVALIATION => false,
                UserEngagement::ALERT_REVALUATION_NEAR => false,
            ],
        ]);

        $this->artisan('engagement:dispatch')->assertSuccessful();

        Queue::assertNothingPushed();
    }

    public function testDispatchCommandDryRunDoesNotQueueOrPersistState()
    {
        Queue::fake();

        User::factory()->create([
            'role' => User::ROLE_MANAGER,
            'active' => true,
            'confirmation' => true,
            'last_login_at' => now()->subDays(10),
        ]);

        $this->artisan('engagement:dispatch --dry-run')
            ->expectsOutput('Dry run mode enabled: no emails were queued and no engagement state was persisted.')
            ->expectsOutput('Engagement dispatch finished.')
            ->assertSuccessful();

        Queue::assertNothingPushed();
        $this->assertDatabaseCount('user_engagements', 0);
    }

    public function testDispatchStoresAbVariantInLastPayloadMeta()
    {
        Queue::fake();

        User::factory()->create([
            'role' => User::ROLE_MANAGER,
            'active' => true,
            'confirmation' => true,
            'last_login_at' => now()->subDays(10),
        ]);

        $this->artisan('engagement:dispatch')->assertSuccessful();

        $engagement = UserEngagement::query()->first();
        $variant = data_get($engagement?->last_payload, 'meta.ab_variant');

        $this->assertNotNull($variant);
        $this->assertContains($variant, ['a', 'b']);
    }

    public function testDispatchSkipsRevaluationNearReasonForFreePlanUsers(): void
    {
        Queue::fake();

        /** @var User $user */
        $user = User::factory()->create([
            'role' => User::ROLE_MANAGER,
            'active' => true,
            'confirmation' => true,
            'last_login_at' => now(),
        ]);

        UserPlans::create([
            'user_id' => $user->id,
            'plan_type' => FeatureAbstract::FEATURE_PLAN_TYPE_FREE,
            'start_date' => now()->subDay()->format('Y-m-d'),
            'end_date' => now()->addDays(30)->format('Y-m-d'),
            'status' => UserPlans::STATUS_ACTIVE,
        ]);

        $client = Client::factory()->create([
            'user_id' => $user->id,
            'birthdate' => '1990-01-01',
            'height_cm' => 175,
            'weight_kg' => 80,
        ]);

        $this->actingAs($user, 'web');
        $save = Avaliation::fSave([
            'client_id' => $client->id,
            'date' => now()->format('Y-m-d'),
            'weight_kg' => 80.0,
            'calculate_perc_fat_by' => Avaliation::CALCULATE_PERC_FAT_BY_BIOIMPEDANCE,
        ]);
        $this->assertFalse($save->isError(), $save->getMessage());

        $avaliation = $save->getValueFromResponse('Avaliation');
        DB::table('avaliations')
            ->where('id', $avaliation->id)
            ->update(['revaluation_date' => now()->addDay()->format('Y-m-d')]);

        $this->artisan('engagement:dispatch')->assertSuccessful();

        Queue::assertNothingPushed();
    }

    public function testDispatchIncludesRevaluationNearReasonForPremiumUsers(): void
    {
        Queue::fake();

        /** @var User $user */
        $user = User::factory()->create([
            'role' => User::ROLE_MANAGER,
            'active' => true,
            'confirmation' => true,
            'last_login_at' => now(),
        ]);

        UserPlans::create([
            'user_id' => $user->id,
            'plan_type' => FeatureAbstract::FEATURE_PLAN_TYPE_PREMIUM,
            'start_date' => now()->subDay()->format('Y-m-d'),
            'end_date' => now()->addDays(30)->format('Y-m-d'),
            'status' => UserPlans::STATUS_ACTIVE,
        ]);

        $client = Client::factory()->create([
            'user_id' => $user->id,
            'birthdate' => '1990-01-01',
            'height_cm' => 175,
            'weight_kg' => 80,
        ]);

        $this->actingAs($user, 'web');
        $save = Avaliation::fSave([
            'client_id' => $client->id,
            'date' => now()->format('Y-m-d'),
            'weight_kg' => 80.0,
            'calculate_perc_fat_by' => Avaliation::CALCULATE_PERC_FAT_BY_BIOIMPEDANCE,
        ]);
        $this->assertFalse($save->isError(), $save->getMessage());

        $avaliation = $save->getValueFromResponse('Avaliation');
        DB::table('avaliations')
            ->where('id', $avaliation->id)
            ->update(['revaluation_date' => now()->addDay()->format('Y-m-d')]);

        $this->artisan('engagement:dispatch')->assertSuccessful();

        Queue::assertPushed(SendEngagementDigestEmailJob::class, function (SendEngagementDigestEmailJob $job) use ($user) {
            if ($job->userId !== $user->id) {
                return false;
            }

            $reasonTypes = array_column($job->payload['reasons'] ?? [], 'type');
            return in_array(UserEngagement::ALERT_REVALUATION_NEAR, $reasonTypes, true);
        });
    }
}
