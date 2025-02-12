<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Client;

class AvaliationFactory extends Factory
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
            'date' => $this->faker->dateTimeBetween('-1 month', '-1 day')->format('Y-m-d'),
            'weight_kg' => $this->faker->numberBetween(50, 110),
            'height_cm' => $this->faker->numberBetween(150, 200),
            'body_fat_perc' => $this->faker->randomFloat(2, 8, 45),
            'skeletal_muscle_perc' => function() {
                if (true === $this->faker->boolean()) {
                    return null;
                }
                return $this->faker->numberBetween(25, 45);
            },
            'visceral_fat_kg' => function() {
                if (true === $this->faker->boolean()) {
                    return null;
                }
                return $this->faker->randomFloat(2, 1, 6);
            },
            'waist_circumference_cm' => function() {
                if (true === $this->faker->boolean()) {
                    return null;
                }
                return $this->faker->numberBetween(60, 110);
            },
        ];
    }
}
