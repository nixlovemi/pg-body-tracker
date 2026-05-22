<?php

namespace Tests\Feature\Engagement;

use App\Jobs\SendEngagementDigestEmailJob;
use App\Models\User;
use App\Models\UserEngagement;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
