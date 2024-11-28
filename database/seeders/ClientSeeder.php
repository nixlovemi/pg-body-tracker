<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Client;
use App\Models\Goal;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (
            User::where('role', User::ROLE_MANAGER)
                ->where('active', true)
                ->get() as $user
        ) {
            for ($i = 0; $i < 5; $i++) {
                $data = ['user_id' => $user->id];

                if (0 === $i) {
                    $data['email'] = null;
                    $data['phone'] = null;
                }

                $Client = Client::factory()->create($data);
                Goal::factory()
                    ->create([
                        'client_id' => $Client->id,
                        'target_weight' => number_format($Client->weight * (1 - random_int(10, 20) / 100), 2, '.', ''),
                    ]);
            }
        }
    }
}
