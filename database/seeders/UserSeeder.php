<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserInfo;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // root
        User::factory()
            ->count(1)
            ->create([
                'password_reset_token' => null,
                'role' => User::ROLE_ROOT,
                'active' => true,
            ]);

        // user roles
        for ($i = 0; $i <= 8; $i++) {
            foreach (array_keys(User::fGetRoles()) as $role) {
                $createArray = [
                    'password_reset_token' => null,
                    'role' => $role,
                ];

                // first user without picture
                if ($i == 0) {
                    $createArray['picture_url'] = null;
                }

                $UserCol = User::factory()
                    ->count(1)
                    ->create($createArray);
                $User = $UserCol[0] ?? null;
                if (!$User) {
                    continue;
                }

                // if is manager
                if ($User->isManager()) {
                    UserInfo::factory()
                        ->count(1)
                        ->create([
                            'user_id' => $User->id,
                        ]);
                }
            }
        }
    }
}
