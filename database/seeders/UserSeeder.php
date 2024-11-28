<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

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
            foreach (array_keys(User::USER_ROLES) as $role) {
                $createArray = [
                    'password_reset_token' => null,
                    'role' => $role,
                ];

                // first user without picture
                if ($i == 0) {
                    $createArray['picture_url'] = null;
                }

                User::factory()
                    ->count(1)
                    ->create($createArray);
            }
        }
    }
}
