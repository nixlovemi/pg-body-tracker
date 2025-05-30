<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class UserFactory extends Factory
{
    private const PATH_USER_IMAGES = '/demo/users/';
    private array $userImages = [
        'f' => [
            self::PATH_USER_IMAGES . 'user-10.jpg',
            self::PATH_USER_IMAGES . 'user-11.jpg',
            self::PATH_USER_IMAGES . 'user-12.jpg',
            self::PATH_USER_IMAGES . 'user-13.jpg',
            self::PATH_USER_IMAGES . 'user-14.jpg',
            self::PATH_USER_IMAGES . 'user-15.jpg',
        ],
        'm' => [
            self::PATH_USER_IMAGES . 'user-1.jpg',
            self::PATH_USER_IMAGES . 'user-2.jpg',
            self::PATH_USER_IMAGES . 'user-3.jpg',
            self::PATH_USER_IMAGES . 'user-4.jpg',
            self::PATH_USER_IMAGES . 'user-5.jpg',
            self::PATH_USER_IMAGES . 'user-6.jpg',
            self::PATH_USER_IMAGES . 'user-7.jpg',
            self::PATH_USER_IMAGES . 'user-8.jpg',
            self::PATH_USER_IMAGES . 'user-9.jpg',
        ],
    ];

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $gender = $this->faker->randomElement(['f', 'm']);

        return [
            'first_name' => $this->faker->firstName($gender),
            'last_name' => $this->faker->lastName($gender),
            'email' => $this->faker->unique()->safeEmail(),
            'picture_url' => function() use ($gender) {
                return $this->faker->randomElement($this->userImages[$gender]);
            },
            'password' => User::fPasswordHash('Mudar123'),
            'password_reset_token' => null,
            'role' => $this->faker->randomElement(array_keys(User::fGetRoles())),
            'active' => $this->faker->randomElement([true, false]),
            'confirmation' => function() {
                if ($this->faker->boolean(80)) {
                    return true; // 80% chance of being confirmed
                }

                return false;
            },
        ];
    }
}
