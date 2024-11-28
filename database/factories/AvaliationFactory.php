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
            'date' => $this->faker->dateTimeBetween('+1 day', '+1 month')->format('Y-m-d'),
            'weight_kg' => $this->faker->numberBetween(50, 110),
            'height_cm' => $this->faker->numberBetween(150, 200),
            'body_fat_perc' => $this->faker->randomFloat(2, 8, 45),
            'skeletal_muscle_mass_kg' => $this->faker->randomFloat(2, 10, 40),
            'muscle_rate_perc' => $this->faker->randomFloat(2, 8, 35),
            'subcutaneous_fat_perc' => $this->faker->randomFloat(2, 8, 35),
            'visceral_fat_perc' => $this->faker->randomFloat(2, 8, 35),
            'body_water_perc' => $this->faker->randomFloat(2, 8, 35),
            'skeletal_muscle_perc' => $this->faker->randomFloat(2, 8, 35),
            'muscle_mass_kg' => $this->faker->randomFloat(2, 10, 40),
            'bone_mass_kg' => $this->faker->randomFloat(2, 1, 10),
            'protein_perc' => $this->faker->randomFloat(2, 8, 35),
        ];
    }
}
