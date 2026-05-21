<?php

namespace Tests\Feature\Profile;

use App\Models\User;
use App\Models\UserInfo;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserInfoFactorySeederTest extends TestCase
{
    use RefreshDatabase;

    public function testUserInfoFactoryProfessionalStateSetsProfessionalMode()
    {
        /** @var User $user */
        $user = User::factory()->create([
            'role' => User::ROLE_MANAGER,
            'active' => true,
            'confirmation' => true,
        ]);
        $this->actingAs($user, 'web');

        $userInfo = UserInfo::factory()
            ->professional()
            ->create([
                'user_id' => $user->id,
            ]);

        $this->assertEquals(UserInfo::EVALUATION_MODE_PROFESSIONAL, $userInfo->evaluation_mode);
    }

    public function testUserSeederCreatesManagerWithProfessionalEvaluationMode()
    {
        /** @var User $root */
        $root = User::factory()->create([
            'role' => User::ROLE_ROOT,
            'active' => true,
            'confirmation' => true,
        ]);
        $this->actingAs($root, 'web');

        $this->seed(UserSeeder::class);

        $hasProfessionalManager = UserInfo::query()
            ->where('evaluation_mode', UserInfo::EVALUATION_MODE_PROFESSIONAL)
            ->whereHas('user', function ($query) {
                $query->where('role', User::ROLE_MANAGER);
            })
            ->exists();

        $this->assertTrue($hasProfessionalManager);
    }
}
