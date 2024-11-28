<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Client;

class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $gender = $this->faker->randomElement(['f', 'm']);

        return [
            'user_id' => function() {
                return User::whereIn('role', [User::ROLE_MANAGER])
                    ->where('active', true)
                    ->inRandomOrder()
                    ->first();
            },
            'first_name' => $this->faker->firstName($gender),
            'last_name' => $this->faker->lastName($gender),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'gender' => function() use ($gender) {
                switch ($gender) {
                    case 'f':
                        return Client::GENDER_FEMALE;
                    default:
                        return Client::GENDER_MALE;
                }
            },
            'birthdate' => $this->faker->date('Y-m-d', '-18 years'),
            'height' => $this->faker->numberBetween(150, 200),
        ];
    }
}
