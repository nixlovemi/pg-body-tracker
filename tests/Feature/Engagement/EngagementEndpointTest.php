<?php

namespace Tests\Feature\Engagement;

use App\Jobs\SendEngagementDigestEmailJob;
use App\Mail\EngagementDigest;
use App\Models\User;
use App\Models\UserEngagement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EngagementEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function testDispatchEndpointRunsCommandWhenKeyIsValid()
    {
        Queue::fake();
        $this->setEngagementEndpointToken('endpoint-secret');

        User::factory()->create([
            'role' => User::ROLE_MANAGER,
            'active' => true,
            'confirmation' => true,
            'last_login_at' => now()->subDays(10),
        ]);

        $response = $this->postJson(route('app.engagement.dispatch'), [], [
            'X-Engagement-Key' => 'endpoint-secret',
        ]);

        $response->assertOk();
        $response->assertJsonPath('message', 'Engagement dispatch finished.');

        Queue::assertPushed(SendEngagementDigestEmailJob::class, 1);
        $this->assertDatabaseCount('user_engagements', 1);
    }

    public function testDispatchEndpointRejectsInvalidKey()
    {
        Queue::fake();
        $this->setEngagementEndpointToken('endpoint-secret');

        User::factory()->create([
            'role' => User::ROLE_MANAGER,
            'active' => true,
            'confirmation' => true,
            'last_login_at' => now()->subDays(10),
        ]);

        $response = $this->postJson(route('app.engagement.dispatch'), [], [
            'X-Engagement-Key' => 'wrong-secret',
        ]);

        $response->assertForbidden();
        Queue::assertNothingPushed();
        $this->assertDatabaseCount('user_engagements', 0);
    }

    public function testUnsubscribeEndpointDisablesEngagementEmailsWithValidSignedUrl()
    {
        $user = User::factory()->create();

        $unsubscribeUrl = URL::temporarySignedRoute(
            'app.engagement.unsubscribe',
            now()->addMinutes(30),
            ['codedId' => $user->codedId]
        );

        $response = $this->get($unsubscribeUrl);

        $response->assertOk();
        $response->assertViewIs('app.engagement-unsubscribed');
        $this->assertDatabaseHas('user_engagements', [
            'user_id' => $user->id,
            'opt_out' => true,
        ]);
    }

    public function testUnsubscribeEndpointRejectsInvalidSignature()
    {
        $user = User::factory()->create();

        $invalidUrl = route('app.engagement.unsubscribe', [
            'codedId' => $user->codedId,
            'expires' => now()->addMinutes(30)->timestamp,
            'signature' => 'invalid',
        ]);

        $response = $this->get($invalidUrl);

        $response->assertStatus(419);
        $this->assertDatabaseMissing('user_engagements', [
            'user_id' => $user->id,
            'opt_out' => true,
        ]);
    }

    public function testEngagementEmailContainsUnsubscribeLink()
    {
        $user = User::factory()->create();

        $mailable = new EngagementDigest($user, [
            'reasons' => [
                ['type' => 'inactive_login', 'days' => 10],
            ],
            'meta' => ['ab_variant' => 'a'],
        ]);

        $html = $mailable->render();

        $this->assertStringContainsString('engagement/unsubscribe/' . $user->codedId, $html);
    }

    private function setEngagementEndpointToken(string $token): void
    {
        putenv('ENGAGEMENT_ENDPOINT_TOKEN=' . $token);
        $_ENV['ENGAGEMENT_ENDPOINT_TOKEN'] = $token;
        $_SERVER['ENGAGEMENT_ENDPOINT_TOKEN'] = $token;
    }
}
