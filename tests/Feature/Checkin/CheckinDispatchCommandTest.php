<?php

namespace Tests\Feature\Checkin;

use App\Helpers\Feature\FeatureAbstract;
use App\Helpers\SysUtils;
use App\Mail\SendCheckinFollowupLink;
use App\Models\CheckinConfig;
use App\Models\Client;
use App\Models\User;
use App\Models\UserPlans;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CheckinDispatchCommandTest extends TestCase
{
    use RefreshDatabase;

    public function testDispatchDueSendsLinksAndUpdatesScheduleForDuePremiumNutritionistConfigs(): void
    {
        Mail::fake();

        /** @var User $nutritionist */
        $nutritionist = User::factory()->create([
            'role' => User::ROLE_MANAGER,
            'active' => true,
            'confirmation' => true,
        ]);
        $this->createPlanForUser($nutritionist, FeatureAbstract::FEATURE_PLAN_TYPE_PREMIUM);

        $client = Client::factory()->create([
            'user_id' => $nutritionist->id,
            'email' => 'client1@example.com',
        ]);

        $config = $this->createCheckinConfig(
            $client->id,
            5,
            now()->subDay()->format('Y-m-d')
        );

        $today = SysUtils::timezoneNow('Y-m-d');

        $this->artisan('checkin:dispatch-due')
            ->expectsOutput('Check-in dispatch finished.')
            ->assertSuccessful();

        Mail::assertSent(SendCheckinFollowupLink::class, 1);

        $config->refresh();
        $this->assertSame($today, optional($config->last_checkin_sent_date)->format('Y-m-d'));
        $this->assertNotNull($config->last_checkin_sent_at);
        $this->assertSame(0, (int) $config->unanswered_reminders_sent);
        $this->assertSame(Carbon::parse($today)->addDays(5)->format('Y-m-d'), optional($config->next_checkin_date)->format('Y-m-d'));
    }

    public function testDispatchDueDryRunDoesNotSendOrPersistScheduleChanges(): void
    {
        Mail::fake();

        /** @var User $nutritionist */
        $nutritionist = User::factory()->create([
            'role' => User::ROLE_MANAGER,
            'active' => true,
            'confirmation' => true,
        ]);
        $this->createPlanForUser($nutritionist, FeatureAbstract::FEATURE_PLAN_TYPE_PREMIUM);

        $client = Client::factory()->create([
            'user_id' => $nutritionist->id,
            'email' => 'client2@example.com',
        ]);

        $config = $this->createCheckinConfig(
            $client->id,
            7,
            now()->subDay()->format('Y-m-d')
        );

        $originalNextDate = optional($config->next_checkin_date)->format('Y-m-d');

        $this->artisan('checkin:dispatch-due --dry-run')
            ->expectsOutput('Check-in dispatch finished.')
            ->assertSuccessful();

        Mail::assertNotSent(SendCheckinFollowupLink::class);

        $config->refresh();
        $this->assertNull($config->last_checkin_sent_date);
        $this->assertNull($config->last_checkin_sent_at);
        $this->assertSame(0, (int) $config->unanswered_reminders_sent);
        $this->assertSame($originalNextDate, optional($config->next_checkin_date)->format('Y-m-d'));
    }

    public function testDispatchDueUserIdScopesByNutritionistManagerIdNotClientId(): void
    {
        Mail::fake();

        /** @var User $nutritionistA */
        $nutritionistA = User::factory()->create([
            'role' => User::ROLE_MANAGER,
            'active' => true,
            'confirmation' => true,
        ]);
        $this->createPlanForUser($nutritionistA, FeatureAbstract::FEATURE_PLAN_TYPE_PREMIUM);

        /** @var User $nutritionistB */
        $nutritionistB = User::factory()->create([
            'role' => User::ROLE_MANAGER,
            'active' => true,
            'confirmation' => true,
        ]);
        $this->createPlanForUser($nutritionistB, FeatureAbstract::FEATURE_PLAN_TYPE_PREMIUM);

        $clientA = Client::factory()->create([
            'user_id' => $nutritionistA->id,
            'email' => 'clienta@example.com',
        ]);

        $configA = $this->createCheckinConfig(
            $clientA->id,
            7,
            now()->subDay()->format('Y-m-d')
        );

        // Scope by another nutritionist (manager) id. If --user_id were treated as client id,
        // this due config could be incorrectly matched.
        $this->artisan('checkin:dispatch-due --user_id=' . $nutritionistB->id)
            ->expectsOutput('Check-in dispatch finished.')
            ->assertSuccessful();

        Mail::assertNotSent(SendCheckinFollowupLink::class);

        $configA->refresh();
        $this->assertNull($configA->last_checkin_sent_date);
    }

    public function testDispatchDueResendsAfterLinkExpirationWhenUnansweredAndWithinGlobalMaxReminders(): void
    {
        Mail::fake();

        $this->setGlobalMaxRemindersPerCycle(1);

        /** @var User $nutritionist */
        $nutritionist = User::factory()->create([
            'role' => User::ROLE_MANAGER,
            'active' => true,
            'confirmation' => true,
        ]);
        $this->createPlanForUser($nutritionist, FeatureAbstract::FEATURE_PLAN_TYPE_PREMIUM);

        $client = Client::factory()->create([
            'user_id' => $nutritionist->id,
            'email' => 'client-reminder@example.com',
        ]);

        $config = $this->createCheckinConfig(
            $client->id,
            7,
            now()->addDays(3)->format('Y-m-d'),
            24,
            now()->subHours(26),
            0,
            null
        );

        $this->artisan('checkin:dispatch-due')->assertSuccessful();

        Mail::assertSent(SendCheckinFollowupLink::class, 1);

        $config->refresh();
        $this->assertSame(1, (int) $config->unanswered_reminders_sent);
        $this->assertNotNull($config->last_checkin_sent_at);
    }

    public function testDispatchDueDoesNotResendAfterReachingGlobalMaxReminders(): void
    {
        Mail::fake();

        $this->setGlobalMaxRemindersPerCycle(1);

        /** @var User $nutritionist */
        $nutritionist = User::factory()->create([
            'role' => User::ROLE_MANAGER,
            'active' => true,
            'confirmation' => true,
        ]);
        $this->createPlanForUser($nutritionist, FeatureAbstract::FEATURE_PLAN_TYPE_PREMIUM);

        $client = Client::factory()->create([
            'user_id' => $nutritionist->id,
            'email' => 'client-reminder-max@example.com',
        ]);

        $config = $this->createCheckinConfig(
            $client->id,
            7,
            now()->addDays(3)->format('Y-m-d'),
            24,
            now()->subHours(26),
            1,
            null
        );

        $this->artisan('checkin:dispatch-due')->assertSuccessful();

        Mail::assertNotSent(SendCheckinFollowupLink::class);

        $config->refresh();
        $this->assertSame(1, (int) $config->unanswered_reminders_sent);
    }

    private function createPlanForUser(User $user, string $planType): void
    {
        UserPlans::create([
            'user_id' => $user->id,
            'plan_type' => $planType,
            'start_date' => now()->subDay()->format('Y-m-d'),
            'end_date' => now()->addDays(30)->format('Y-m-d'),
            'status' => UserPlans::STATUS_ACTIVE,
        ]);
    }

    private function createCheckinConfig(
        int $clientId,
        int $intervalDays,
        string $nextCheckinDate,
        int $linkExpiresHours = 24,
        ?Carbon $lastCheckinSentAt = null,
        int $unansweredRemindersSent = 0,
        ?string $lastCheckinDate = null
    ): CheckinConfig
    {
        $lastCheckinSentDate = $lastCheckinSentAt?->format('Y-m-d');

        $id = DB::table('checkin_configs')->insertGetId([
            'client_id' => $clientId,
            'active' => true,
            'interval_days' => $intervalDays,
            'link_expires_hours' => $linkExpiresHours,
            'fields_config' => json_encode([]),
            'next_checkin_date' => $nextCheckinDate,
            'last_checkin_date' => $lastCheckinDate,
            'last_checkin_sent_date' => $lastCheckinSentDate,
            'last_checkin_sent_at' => $lastCheckinSentAt,
            'unanswered_reminders_sent' => $unansweredRemindersSent,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return CheckinConfig::query()->findOrFail($id);
    }

    private function setGlobalMaxRemindersPerCycle(int $value): void
    {
        putenv('CHECKIN_MAX_REMINDERS_PER_CYCLE=' . $value);
        $_ENV['CHECKIN_MAX_REMINDERS_PER_CYCLE'] = (string) $value;
        $_SERVER['CHECKIN_MAX_REMINDERS_PER_CYCLE'] = (string) $value;
    }
}
