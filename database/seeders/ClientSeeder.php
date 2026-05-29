<?php

namespace Database\Seeders;

use App\Helpers\Feature\FeatureAbstract;
use App\Helpers\SysUtils;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Client;
use App\Models\Goal;
use App\Models\Avaliation;
use App\Models\CheckinConfig;
use App\Models\UserPlans;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $premiumManagerId = User::query()
            ->where('role', User::ROLE_MANAGER)
            ->where('active', true)
            ->whereHas('plans', function ($query) {
                $query->where('status', UserPlans::STATUS_ACTIVE)
                    ->where('plan_type', FeatureAbstract::FEATURE_PLAN_TYPE_PREMIUM);
            })
            ->orderBy('id')
            ->value('id');

        $seededRichPremiumClient = false;

        foreach (
            User::where('role', User::ROLE_MANAGER)
                ->where('active', true)
                ->get() as $user
        ) {
            $currentUser = SysUtils::getLoggedInUser();
            $switchedUser = false;

            try {
                if (!$currentUser || $currentUser->id !== $user->id) {
                    if (!SysUtils::loginUserTempById($user->id, 5)) {
                        continue;
                    }

                    $switchedUser = true;
                }

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
                            'target_weight_kg' => number_format($Client->weight_kg * (1 - (random_int(10, 20) / 100)), 1, '.', ''),
                        ]);
                    Avaliation::factory()
                        ->create(array_merge(
                            [
                                'client_id' => $Client->id,
                                'height_cm' => $Client->height_cm,
                            ],
                        ));

                    if (
                        !$seededRichPremiumClient
                        && $premiumManagerId
                        && (int) $user->id === (int) $premiumManagerId
                        && $i === 1
                    ) {
                        $this->seedRichInsightsData($Client);
                        $seededRichPremiumClient = true;
                    }
                }
            } finally {
                if ($switchedUser) {
                    if ($currentUser) {
                        SysUtils::loginUser($currentUser);
                    } else {
                        SysUtils::logout(false);
                    }
                }
            }
        }
    }

    private function seedRichInsightsData(Client $client): void
    {
        $now = now()->setTimezone(env('APP_TIME_ZONE'))->startOfDay();

        // Keep seeded demo deterministic for insights card behavior.
        $client->avaliations()->delete();

        $timeline = [
            ['daysAgo' => 35, 'weight' => $client->weight_kg],
            ['daysAgo' => 28, 'weight' => $client->weight_kg - 0.7],
            ['daysAgo' => 21, 'weight' => $client->weight_kg - 1.2],
            ['daysAgo' => 14, 'weight' => $client->weight_kg - 1.8],
            ['daysAgo' => 7, 'weight' => $client->weight_kg - 2.0],
            ['daysAgo' => 5, 'weight' => $client->weight_kg - 1.7],
            ['daysAgo' => 3, 'weight' => $client->weight_kg - 2.2],
            ['daysAgo' => 1, 'weight' => $client->weight_kg - 2.4],
        ];

        foreach ($timeline as $row) {
            Avaliation::factory()->create([
                'client_id' => $client->id,
                'date' => $now->copy()->subDays((int) $row['daysAgo'])->format('Y-m-d'),
                'age' => $client->getAge(),
                'weight_kg' => round((float) $row['weight'], 1),
                'height_cm' => $client->height_cm,
                'calculate_perc_fat_by' => Avaliation::CALCULATE_PERC_FAT_BY_BIOIMPEDANCE,
            ]);
        }

        $goal = $client->goals()->orderByDesc('id')->first();
        $goalTargetWeight = max(30.0, (float) $client->weight_kg - 8.0);
        $goalStartAt = $now->copy()->subDays(20);
        $goalDeadlineAt = $now->copy()->addDays(40);

        if ($goal) {
            $goal->objective = Goal::OBJECTIVE_WEIGHT_LOSS;
            $goal->initial_weight_kg = round((float) $client->weight_kg, 1);
            $goal->target_weight_kg = round($goalTargetWeight, 1);
            $goal->deadline = $goalDeadlineAt->format('Y-m-d');
            $goal->created_at = $goalStartAt->copy();
            $goal->updated_at = $now->copy();
            $goal->save();
        } else {
            Goal::factory()->create([
                'client_id' => $client->id,
                'objective' => Goal::OBJECTIVE_WEIGHT_LOSS,
                'initial_weight_kg' => round((float) $client->weight_kg, 1),
                'target_weight_kg' => round($goalTargetWeight, 1),
                'deadline' => $goalDeadlineAt->format('Y-m-d'),
                'created_at' => $goalStartAt->copy(),
                'updated_at' => $now->copy(),
            ]);
        }

        CheckinConfig::query()->updateOrCreate(
            ['client_id' => $client->id],
            [
                'active' => true,
                'interval_days' => 7,
                'link_expires_hours' => 24,
                'next_checkin_date' => $now->copy()->addDays(2)->format('Y-m-d'),
                'last_checkin_date' => $now->copy()->subDays(6)->format('Y-m-d'),
                'last_checkin_sent_date' => $now->copy()->subDays(2)->format('Y-m-d'),
                'last_checkin_sent_at' => $now->copy()->subDays(2)->setTime(8, 0, 0),
                'unanswered_reminders_sent' => 1,
            ]
        );
    }
}
