<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class UserInfoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $arrLogos = [
            '/demo/logos/logo-01.png',
            '/demo/logos/logo-02.jpg',
            '/demo/logos/logo-03.png',
        ];

        return [
            'user_id' => function() {
                return User::where('role', '=', User::ROLE_MANAGER)
                    ->leftJoin('user_infos', 'user_infos.user_id', '=', 'user.id')
                    ->whereRaw('user_infos.id IS NULL')
                    ->inRandomOrder()
                    ->first();
            },
            'logo_url' => function() use ($arrLogos) {
                return $this->faker->optional(0.6)->randomElement($arrLogos);
            },
            'title' => $this->faker->randomElement(['medic', 'personal trainer', 'nutritionist']),
            'license_text' => function() {
                return $this->faker->word() . $this->faker->randomNumber(5);
            },
            'whatsapp_phone' => $this->faker->optional(0.6)->phoneNumber(),
            'link_telegram' => $this->faker->optional(0.6)->url(),
            'link_facebook' => $this->faker->optional(0.6)->url(),
            'link_instagram' => $this->faker->optional(0.6)->url(),
            'link_twitter' => $this->faker->optional(0.6)->url(),
            'link_youtube' => $this->faker->optional(0.6)->url(),
            'link_website' => $this->faker->optional(0.6)->url(),
        ];
    }
}
