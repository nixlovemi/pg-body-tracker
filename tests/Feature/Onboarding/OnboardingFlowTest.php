<?php

namespace Tests\Feature\Onboarding;

use App\Models\Avaliation;
use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class OnboardingFlowTest extends TestCase
{
    use DatabaseTransactions;

    public function testDashboardShowsClientCtaWhenNoClientsAndNoAvaliations()
    {
        $this->signInManager();

        $response = $this->get(route('app.dashboard.index'));

        $response->assertOk();
        $response->assertSee(__('messages.pages.dashboard.onboarding.ctaClient'));
        $response->assertDontSee(__('messages.pages.dashboard.onboarding.ctaAvaliation'));
    }

    public function testDashboardShowsAvaliationCtaWhenClientExistsAndNoAvaliations()
    {
        $user = $this->signInManager();
        $this->createClientFor($user);

        $response = $this->get(route('app.dashboard.index'));

        $response->assertOk();
        $response->assertSee(__('messages.pages.dashboard.onboarding.ctaAvaliation'));
        $response->assertDontSee(__('messages.pages.dashboard.onboarding.ctaClient'));
    }

    public function testClientAddWithPrefillSelfDefaultsToRedirectAfterSave()
    {
        $this->signInManager();

        $response = $this->get(route('app.client.add', ['prefillSelf' => 1]));

        $response->assertOk();
        $response->assertSee('id="f-onboarding-create-first-avaliation"', false);
        $response->assertSee('value="1"', false);
    }

    public function testFirstClientSaveWithOnboardingFlagRedirectsToAvaliationIndex()
    {
        $user = $this->signInManager();

        $response = $this->post(route('app.client.doSave'), [
            'f-cid' => null,
            'f-name' => 'John',
            'f-surname' => 'Doe',
            'f-email' => 'john.doe@example.com',
            'f-phone' => null,
            'f-bsex' => Client::GENDER_MALE,
            'f-birth' => '01/01/1990',
            'f-height' => 180,
            'f-weight' => '80,000',
            'f-onboarding-create-first-avaliation' => '1',
        ]);

        $client = Client::where('user_id', $user->id)->first();

        $this->assertNotNull($client);
        $response->assertRedirect(route('app.avaliation.index', [
            'openAvaliation' => 1,
            'openAvaliationCID' => $client->codedId,
        ]));
    }

    public function testAvaliationIndexShowsEmptyStateWhenClientExistsWithoutAvaliations()
    {
        $user = $this->signInManager();
        $this->createClientFor($user);

        $response = $this->get(route('app.avaliation.index'));

        $response->assertOk();
        $response->assertSee('id="btn-add-avaliations-empty-state"', false);
        $response->assertSee(__('messages.pages.avaliation.index.emptyCta'));
    }

    public function testAvaliationIndexHidesEmptyStateAfterFirstAvaliation()
    {
        $user = $this->signInManager();
        $client = $this->createClientFor($user);
        $this->createAvaliationFor($client);

        $response = $this->get(route('app.avaliation.index'));

        $response->assertOk();
        $response->assertDontSee('id="btn-add-avaliations-empty-state"', false);
        $response->assertSee('id="btn-add-avaliations"', false);
    }

    private function signInManager(): User
    {
        /** @var User $user */
        $user = User::factory()->create([
            'role' => User::ROLE_MANAGER,
            'active' => true,
            'confirmation' => true,
        ]);

        $this->actingAs($user, 'web');

        return $user;
    }

    private function createClientFor(User $user): Client
    {
        return Client::factory()->create([
            'user_id' => $user->id,
            'gender' => Client::GENDER_MALE,
            'birthdate' => '1990-01-01',
            'height_cm' => 180,
            'weight_kg' => 80,
        ]);
    }

    private function createAvaliationFor(Client $client): Avaliation
    {
        return Avaliation::factory()->create([
            'client_id' => $client->id,
        ]);
    }
}
