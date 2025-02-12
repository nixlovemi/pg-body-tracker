<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Client;
use App\Models\Goal;

class GoalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'client_id' => function() {
                return Client::inRandomOrder()->first();
            },
            'objective' => $this->faker->randomElement(array_keys(Goal::fGetObjectives())),
            'initial_weight_kg' => function(array $attributes) {
                // get Client from client_id
                $client = Client::find($attributes['client_id']);

                return $client->getCurrentWeight();
            },
            'target_weight_kg' => $this->faker->numberBetween(50, 110),
            'deadline' => $this->faker->dateTimeBetween('+1 month', '+6 months')->format('Y-m-d'),
        ];
    }
}
