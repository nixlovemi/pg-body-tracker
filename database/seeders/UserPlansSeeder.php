<?php

namespace Database\Seeders;

use App\Helpers\Feature\FeatureAbstract;
use App\Models\User;
use App\Models\UserPlans;
use Illuminate\Database\Seeder;

class UserPlansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $manager = User::query()
            ->where('role', User::ROLE_MANAGER)
            ->where('active', true)
            ->orderBy('id')
            ->first();

        if (!$manager) {
            return;
        }

        $startDate = now()->format('Y-m-d');
        $endDate = now()->addYear()->format('Y-m-d');

        UserPlans::query()->updateOrCreate(
            [
                'user_id' => $manager->id,
                'status' => UserPlans::STATUS_ACTIVE,
            ],
            [
                'plan_type' => FeatureAbstract::FEATURE_PLAN_TYPE_PREMIUM,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'payment_data' => json_encode([
                    'source' => 'database_seeder',
                    'note' => 'ensure at least one active manager is premium',
                ]),
            ]
        );
    }
}
