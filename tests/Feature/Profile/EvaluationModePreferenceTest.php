<?php

namespace Tests\Feature\Profile;

use App\Models\Client;
use App\Models\User;
use App\Models\UserEngagement;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class EvaluationModePreferenceTest extends TestCase
{
    use DatabaseTransactions;

    public function testProfilePageShowsLicenseHelpAndFieldOrder()
    {
        /** @var User $user */
        $user = User::factory()->create([
            'role' => User::ROLE_MANAGER,
            'active' => true,
            'confirmation' => true,
        ]);
        $this->actingAs($user, 'web');

        $response = $this->get(route('app.user.profile'));

        $response->assertOk();
        $response->assertSee(__('messages.models.UserInfo.licenseTextHelp'));
        $response->assertSeeInOrder([
            __('messages.models.UserInfo.fields.title'),
            __('messages.models.UserInfo.fields.license_text'),
            __('messages.models.UserInfo.fields.evaluation_mode'),
            __('messages.models.UserInfo.fields.whatsapp_phone'),
        ]);
    }

    public function testProfileSavesProfessionalEvaluationModeAndAvaliationModalStartsAdvanced()
    {
        /** @var User $user */
        $user = User::factory()->create([
            'role' => User::ROLE_MANAGER,
            'active' => true,
            'confirmation' => true,
        ]);
        $this->actingAs($user, 'web');

        $response = $this->post(route('app.user.doProfile'), [
            'f-user-name' => $user->first_name,
            'f-user-lname' => $user->last_name,
            'f-userinfo-title' => 'Personal Trainer',
            'f-userinfo-lictext' => 'CREF 12345',
            'f-userinfo-mode' => 'professional',
            'f-userinfo-whats' => null,
            'f-userinfo-telegram' => null,
            'f-userinfo-face' => null,
            'f-userinfo-insta' => null,
            'f-userinfo-twit' => null,
            'f-userinfo-yt' => null,
            'f-userinfo-site' => null,
            'f-engagement-alert-inactive-login' => '1',
            'f-engagement-alert-missing-setup' => '1',
            'f-engagement-alert-birthday-today' => '1',
            'f-engagement-alert-goal-near-deadline' => '0',
            'f-engagement-alert-client-without-recent-avaliation' => '1',
            'f-engagement-alert-revaluation-near' => '0',
        ]);

        $response->assertRedirect(route('app.user.profile'));
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('user_infos', [
            'user_id' => $user->id,
            'evaluation_mode' => 'professional',
        ]);

        $engagement = UserEngagement::where('user_id', $user->id)->first();
        $this->assertNotNull($engagement);
        $this->assertFalse($engagement->opt_out);
        $expectedPreferences = [
            UserEngagement::ALERT_INACTIVE_LOGIN => true,
            UserEngagement::ALERT_MISSING_SETUP => true,
            UserEngagement::ALERT_BIRTHDAY_TODAY => true,
            UserEngagement::ALERT_GOAL_NEAR_DEADLINE => false,
            UserEngagement::ALERT_CLIENT_WITHOUT_RECENT_AVALIATION => true,
            UserEngagement::ALERT_REVALUATION_NEAR => false,
        ];
        $actualPreferences = $engagement->alert_preferences ?? [];
        ksort($expectedPreferences);
        ksort($actualPreferences);
        $this->assertSame($expectedPreferences, $actualPreferences);

        /** @var Client $client */
        $client = Client::factory()->create([
            'user_id' => $user->id,
            'gender' => Client::GENDER_MALE,
            'birthdate' => '1990-01-01',
            'height_cm' => 180,
            'weight_kg' => 80,
        ]);

        $response = $this->get(route('app.avaliation.htmlModalAdd', [
            'cuid' => $client->codedId,
            'cedit' => 1,
        ]));

        $response->assertOk();
        $response->assertSee('Fluxo completo para uso profissional');
        $this->assertMatchesRegularExpression('/id="f-show-advanced"[^>]*checked/', $response->getContent());
    }
}
