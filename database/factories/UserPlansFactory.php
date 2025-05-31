<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Helpers\Feature\FeatureAbstract;

class UserPlansFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => function() {
                return User::where('role', '=', User::ROLE_MANAGER)
                    ->leftJoin('user_plans', 'user_plans.user_id', '=', 'user.id')
                    ->whereRaw('user_plans.id IS NULL')
                    ->inRandomOrder()
                    ->first();
            },
            'plan_type' => function() {
                return $this->faker->randomElement([FeatureAbstract::FEATURE_PLAN_TYPE_FREE, FeatureAbstract::FEATURE_PLAN_TYPE_PREMIUM]);
            },
            'start_date' => $this->faker->dateTimeBetween('-1 month', '-6 months')->format('Y-m-d'),
            'end_date' => function(array $attributes) {
                $startDate = \Carbon\Carbon::createFromFormat('Y-m-d', $attributes['start_date']);
                $monthsToAdd = $this->faker->numberBetween(4, 12);
                return $startDate->addMonths($monthsToAdd)->format('Y-m-d');
            },
            'payment_data' => null,
        ];
    }
}
