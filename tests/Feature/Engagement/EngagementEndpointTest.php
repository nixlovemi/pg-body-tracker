<?php

namespace Tests\Feature\Engagement;

use App\Jobs\SendEngagementDigestEmailJob;
use App\Models\User;
use App\Models\UserEngagement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
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

    private function setEngagementEndpointToken(string $token): void
    {
        putenv('ENGAGEMENT_ENDPOINT_TOKEN=' . $token);
        $_ENV['ENGAGEMENT_ENDPOINT_TOKEN'] = $token;
        $_SERVER['ENGAGEMENT_ENDPOINT_TOKEN'] = $token;
    }
}
