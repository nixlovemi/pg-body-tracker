<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Client;
use App\Models\Avaliation;

class AvaliationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $Client = Client::inRandomOrder()->first();

        return [
            'client_id' => function() use ($Client) {
                return $Client->id;
            },
            'date' => $this->faker->dateTimeBetween('-1 month', '-1 day')->format('Y-m-d'),
            'age' => function() use ($Client) {
                return $Client->getAge();
            },
            'weight_kg' => function() use ($Client) {
                $factor = $this->faker->randomFloat(2, 0.75, 1.25);
                return $Client->weight_kg * $factor;
            },
            'height_cm' => function() use ($Client) {
                return $Client->height_cm;
            },
            'calculate_perc_fat_by' => function() {
                return $this->faker->randomElement(array_keys(Avaliation::fGetCalculatePercFatBy()));
            },

            'body_fat_perc' => function(array $attr) {
                if (Avaliation::CALCULATE_PERC_FAT_BY_BIOIMPEDANCE !== $attr['calculate_perc_fat_by']) {
                    return null;
                }
                return $this->faker->randomFloat(2, 8, 45);
            },
            'skeletal_muscle_perc' => function(array $attr) {
                if (Avaliation::CALCULATE_PERC_FAT_BY_BIOIMPEDANCE !== $attr['calculate_perc_fat_by']) {
                    return null;
                }
                return $this->faker->randomFloat(2, 25, 45);
            },
            'muscle_mass_perc' => function(array $attr) {
                if (Avaliation::CALCULATE_PERC_FAT_BY_BIOIMPEDANCE !== $attr['calculate_perc_fat_by']) {
                    return null;
                }
                return $this->faker->numberBetween(25, 75);
            },
            'visceral_fat_kg' => function(array $attr) {
                if (Avaliation::CALCULATE_PERC_FAT_BY_BIOIMPEDANCE !== $attr['calculate_perc_fat_by']) {
                    return null;
                }
                return $this->faker->randomFloat(1, 1, 6);
            },
            'basal_metabolism' => function(array $attr) {
                if (Avaliation::CALCULATE_PERC_FAT_BY_BIOIMPEDANCE !== $attr['calculate_perc_fat_by']) {
                    return null;
                }
                return $this->faker->numberBetween(1300, 2900);
            },
            'body_age' => function(array $attr) {
                if (Avaliation::CALCULATE_PERC_FAT_BY_BIOIMPEDANCE !== $attr['calculate_perc_fat_by']) {
                    return null;
                }
                return $this->faker->numberBetween(10, 100);
            },
            'body_water_perc' => function(array $attr) {
                if (Avaliation::CALCULATE_PERC_FAT_BY_BIOIMPEDANCE !== $attr['calculate_perc_fat_by']) {
                    return null;
                }
                return $this->faker->numberBetween(40, 70);
            },
            'bone_mass_kg' => function(array $attr) {
                if (Avaliation::CALCULATE_PERC_FAT_BY_BIOIMPEDANCE !== $attr['calculate_perc_fat_by']) {
                    return null;
                }
                return $this->faker->randomFloat(1, 1, 6);
            },
            'right_arm_lean_mass_kg' => function(array $attr) {
                if (Avaliation::CALCULATE_PERC_FAT_BY_BIOIMPEDANCE !== $attr['calculate_perc_fat_by']) {
                    return null;
                }
                return $this->faker->randomFloat(1, 2, 15);
            },
            'right_arm_lean_mass_perc' => function(array $attr) {
                if (Avaliation::CALCULATE_PERC_FAT_BY_BIOIMPEDANCE !== $attr['calculate_perc_fat_by']) {
                    return null;
                }
                return $this->faker->numberBetween(15, 45);
            },
            'right_arm_fat_kg' => function(array $attr) {
                if (Avaliation::CALCULATE_PERC_FAT_BY_BIOIMPEDANCE !== $attr['calculate_perc_fat_by']) {
                    return null;
                }
                return $this->faker->randomFloat(1, 2, 15);
            },
            'right_arm_fat_perc' => function(array $attr) {
                if (Avaliation::CALCULATE_PERC_FAT_BY_BIOIMPEDANCE !== $attr['calculate_perc_fat_by']) {
                    return null;
                }
                return $this->faker->numberBetween(15, 45);
            },
            'left_arm_lean_mass_kg' => function(array $attr) {
                if (Avaliation::CALCULATE_PERC_FAT_BY_BIOIMPEDANCE !== $attr['calculate_perc_fat_by']) {
                    return null;
                }
                return $this->faker->randomFloat(1, 2, 15);
            },
            'left_arm_lean_mass_perc' => function(array $attr) {
                if (Avaliation::CALCULATE_PERC_FAT_BY_BIOIMPEDANCE !== $attr['calculate_perc_fat_by']) {
                    return null;
                }
                return $this->faker->numberBetween(15, 45);
            },
            'left_arm_fat_kg' => function(array $attr) {
                if (Avaliation::CALCULATE_PERC_FAT_BY_BIOIMPEDANCE !== $attr['calculate_perc_fat_by']) {
                    return null;
                }
                return $this->faker->randomFloat(1, 2, 15);
            },
            'left_arm_fat_perc' => function(array $attr) {
                if (Avaliation::CALCULATE_PERC_FAT_BY_BIOIMPEDANCE !== $attr['calculate_perc_fat_by']) {
                    return null;
                }
                return $this->faker->numberBetween(15, 45);
            },
            'trunk_lean_mass_kg' => function(array $attr) {
                if (Avaliation::CALCULATE_PERC_FAT_BY_BIOIMPEDANCE !== $attr['calculate_perc_fat_by']) {
                    return null;
                }
                return $this->faker->randomFloat(1, 2, 15);
            },
            'trunk_lean_mass_perc' => function(array $attr) {
                if (Avaliation::CALCULATE_PERC_FAT_BY_BIOIMPEDANCE !== $attr['calculate_perc_fat_by']) {
                    return null;
                }
                return $this->faker->numberBetween(15, 45);
            },
            'trunk_fat_kg' => function(array $attr) {
                if (Avaliation::CALCULATE_PERC_FAT_BY_BIOIMPEDANCE !== $attr['calculate_perc_fat_by']) {
                    return null;
                }
                return $this->faker->randomFloat(1, 2, 15);
            },
            'trunk_fat_perc' => function(array $attr) {
                if (Avaliation::CALCULATE_PERC_FAT_BY_BIOIMPEDANCE !== $attr['calculate_perc_fat_by']) {
                    return null;
                }
                return $this->faker->numberBetween(15, 45);
            },
            'right_leg_lean_mass_kg' => function(array $attr) {
                if (Avaliation::CALCULATE_PERC_FAT_BY_BIOIMPEDANCE !== $attr['calculate_perc_fat_by']) {
                    return null;
                }
                return $this->faker->randomFloat(1, 2, 15);
            },
            'right_leg_lean_mass_perc' => function(array $attr) {
                if (Avaliation::CALCULATE_PERC_FAT_BY_BIOIMPEDANCE !== $attr['calculate_perc_fat_by']) {
                    return null;
                }
                return $this->faker->numberBetween(15, 45);
            },
            'right_leg_fat_kg' => function(array $attr) {
                if (Avaliation::CALCULATE_PERC_FAT_BY_BIOIMPEDANCE !== $attr['calculate_perc_fat_by']) {
                    return null;
                }
                return $this->faker->randomFloat(1, 2, 15);
            },
            'right_leg_fat_perc' => function(array $attr) {
                if (Avaliation::CALCULATE_PERC_FAT_BY_BIOIMPEDANCE !== $attr['calculate_perc_fat_by']) {
                    return null;
                }
                return $this->faker->numberBetween(15, 45);
            },
            'left_leg_lean_mass_kg' => function(array $attr) {
                if (Avaliation::CALCULATE_PERC_FAT_BY_BIOIMPEDANCE !== $attr['calculate_perc_fat_by']) {
                    return null;
                }
                return $this->faker->randomFloat(1, 2, 15);
            },
            'left_leg_lean_mass_perc' => function(array $attr) {
                if (Avaliation::CALCULATE_PERC_FAT_BY_BIOIMPEDANCE !== $attr['calculate_perc_fat_by']) {
                    return null;
                }
                return $this->faker->numberBetween(15, 45);
            },
            'left_leg_fat_kg' => function(array $attr) {
                if (Avaliation::CALCULATE_PERC_FAT_BY_BIOIMPEDANCE !== $attr['calculate_perc_fat_by']) {
                    return null;
                }
                return $this->faker->randomFloat(1, 2, 15);
            },
            'left_leg_fat_perc' => function(array $attr) {
                if (Avaliation::CALCULATE_PERC_FAT_BY_BIOIMPEDANCE !== $attr['calculate_perc_fat_by']) {
                    return null;
                }
                return $this->faker->numberBetween(15, 45);
            },

            'chest_circ_cm' => function(array $attr) {
                if (Avaliation::CALCULATE_PERC_FAT_BY_MEASURES !== $attr['calculate_perc_fat_by']) {
                    return null;
                }
                return $this->faker->randomFloat(1, 60, 120);
            },
            'right_arm_circ_cm' => function(array $attr) {
                if (Avaliation::CALCULATE_PERC_FAT_BY_MEASURES !== $attr['calculate_perc_fat_by']) {
                    return null;
                }
                return $this->faker->randomFloat(1, 30, 70);
            },
            'left_arm_circ_cm' => function(array $attr) {
                if (Avaliation::CALCULATE_PERC_FAT_BY_MEASURES !== $attr['calculate_perc_fat_by']) {
                    return null;
                }
                return $this->faker->randomFloat(1, 30, 70);
            },
            'waist_circ_cm' => function(array $attr) {
                if (Avaliation::CALCULATE_PERC_FAT_BY_MEASURES !== $attr['calculate_perc_fat_by']) {
                    return null;
                }
                return $this->faker->randomFloat(1, 60, 120);
            },
            'right_forearm_circ_cm' => function(array $attr) {
                if (Avaliation::CALCULATE_PERC_FAT_BY_MEASURES !== $attr['calculate_perc_fat_by']) {
                    return null;
                }
                return $this->faker->randomFloat(1, 12, 35);
            },
            'left_forearm_circ_cm' => function(array $attr) {
                if (Avaliation::CALCULATE_PERC_FAT_BY_MEASURES !== $attr['calculate_perc_fat_by']) {
                    return null;
                }
                return $this->faker->randomFloat(1, 12, 35);
            },
            'abdomen_circ_cm' => function(array $attr) {
                if (Avaliation::CALCULATE_PERC_FAT_BY_MEASURES !== $attr['calculate_perc_fat_by']) {
                    return null;
                }
                return $this->faker->randomFloat(1, 50, 110);
            },
            'right_thigh_circ_cm' => function(array $attr) {
                if (Avaliation::CALCULATE_PERC_FAT_BY_MEASURES !== $attr['calculate_perc_fat_by']) {
                    return null;
                }
                return $this->faker->randomFloat(1, 60, 120);
            },
            'left_thigh_circ_cm' => function(array $attr) {
                if (Avaliation::CALCULATE_PERC_FAT_BY_MEASURES !== $attr['calculate_perc_fat_by']) {
                    return null;
                }
                return $this->faker->randomFloat(1, 60, 120);
            },
            'hip_circ_cm' => function(array $attr) {
                if (Avaliation::CALCULATE_PERC_FAT_BY_MEASURES !== $attr['calculate_perc_fat_by']) {
                    return null;
                }
                return $this->faker->randomFloat(1, 60, 120);
            },
            'right_calf_circ_cm' => function(array $attr) {
                if (Avaliation::CALCULATE_PERC_FAT_BY_MEASURES !== $attr['calculate_perc_fat_by']) {
                    return null;
                }
                return $this->faker->randomFloat(1, 30, 60);
            },
            'left_calf_circ_cm' => function(array $attr) {
                if (Avaliation::CALCULATE_PERC_FAT_BY_MEASURES !== $attr['calculate_perc_fat_by']) {
                    return null;
                }
                return $this->faker->randomFloat(1, 30, 60);
            },
            'neck_circ_cm' => function(array $attr) {
                if (Avaliation::CALCULATE_PERC_FAT_BY_MEASURES !== $attr['calculate_perc_fat_by']) {
                    return null;
                }
                return $this->faker->randomFloat(1, 20, 60);
            },

            'skin_folds_formula' => function(array $attr) {
                if (Avaliation::CALCULATE_PERC_FAT_BY_SKINFOLD !== $attr['calculate_perc_fat_by']) {
                    return null;
                }
                return $this->faker->randomElement(array_merge([null], array_keys(Avaliation::fGetSkinFoldFormulas())));
            },
            'skin_folds_chest_cm' => function(array $attr) use ($Client) {
                if (Avaliation::CALCULATE_PERC_FAT_BY_SKINFOLD !== $attr['calculate_perc_fat_by']) {
                    return null;
                }

                $can = $Client->gender === Client::GENDER_MALE && $attr['skin_folds_formula'] === Avaliation::SKIN_FOLDS_FORMULA_3_FOLDS_JACKSON_POLLOCK;
                $can = $can || $attr['skin_folds_formula'] === Avaliation::SKIN_FOLDS_FORMULA_7_FOLDS_JACKSON_POLLOCK;

                if (!$can) {
                    return null;
                }

                return $this->faker->randomFloat(1, 0.2, 5);
            },
            'skin_folds_abdominal_cm' => function(array $attr) use ($Client) {
                if (Avaliation::CALCULATE_PERC_FAT_BY_SKINFOLD !== $attr['calculate_perc_fat_by']) {
                    return null;
                }

                $can = $Client->gender === Client::GENDER_MALE && $attr['skin_folds_formula'] === Avaliation::SKIN_FOLDS_FORMULA_3_FOLDS_JACKSON_POLLOCK;
                $can = $can || $attr['skin_folds_formula'] === Avaliation::SKIN_FOLDS_FORMULA_7_FOLDS_JACKSON_POLLOCK;

                if ($can) {
                    return $this->faker->randomFloat(1, 0.3, 6);
                }

                return null;
            },
            'skin_folds_thigh_cm' => function(array $attr) {
                if (Avaliation::CALCULATE_PERC_FAT_BY_SKINFOLD !== $attr['calculate_perc_fat_by']) {
                    return null;
                }

                $can = $attr['skin_folds_formula'] === Avaliation::SKIN_FOLDS_FORMULA_3_FOLDS_JACKSON_POLLOCK;
                $can = $can || $attr['skin_folds_formula'] === Avaliation::SKIN_FOLDS_FORMULA_7_FOLDS_JACKSON_POLLOCK;

                if (!$can) {
                    return null;
                }

                return $this->faker->randomFloat(1, 0.5, 6);
            },
            'skin_folds_tricep_cm' => function(array $attr) use ($Client) {
                if (Avaliation::CALCULATE_PERC_FAT_BY_SKINFOLD !== $attr['calculate_perc_fat_by']) {
                    return null;
                }

                $can = $Client->gender === Client::GENDER_FEMALE && $attr['skin_folds_formula'] === Avaliation::SKIN_FOLDS_FORMULA_3_FOLDS_JACKSON_POLLOCK;
                $can = $can || $attr['skin_folds_formula'] === Avaliation::SKIN_FOLDS_FORMULA_7_FOLDS_JACKSON_POLLOCK;
                $can = $can || $attr['skin_folds_formula'] === Avaliation::SKIN_FOLDS_FORMULA_4_FOLDS_DURNIN_WOMERSLEY;

                if (!$can) {
                    return null;
                }

                return $this->faker->randomFloat(1, 0.4, 5);
            },
            'skin_folds_suprailiac_cm' => function(array $attr) use ($Client) {
                if (Avaliation::CALCULATE_PERC_FAT_BY_SKINFOLD !== $attr['calculate_perc_fat_by']) {
                    return null;
                }

                $can = $Client->gender === Client::GENDER_FEMALE && $attr['skin_folds_formula'] === Avaliation::SKIN_FOLDS_FORMULA_3_FOLDS_JACKSON_POLLOCK;
                $can = $can || $attr['skin_folds_formula'] === Avaliation::SKIN_FOLDS_FORMULA_7_FOLDS_JACKSON_POLLOCK;
                $can = $can || $attr['skin_folds_formula'] === Avaliation::SKIN_FOLDS_FORMULA_4_FOLDS_DURNIN_WOMERSLEY;

                if (!$can) {
                    return null;
                }

                return $this->faker->randomFloat(1, 0.3, 5);
            },
            'skin_folds_axilla_cm' => function(array $attr) {
                if (Avaliation::CALCULATE_PERC_FAT_BY_SKINFOLD !== $attr['calculate_perc_fat_by']) {
                    return null;
                }

                $can = $attr['skin_folds_formula'] === Avaliation::SKIN_FOLDS_FORMULA_7_FOLDS_JACKSON_POLLOCK;
                if (!$can) {
                    return null;
                }

                return $this->faker->randomFloat(1, 0.3, 5);
            },
            'skin_folds_subscapular_cm' => function(array $attr) {
                if (Avaliation::CALCULATE_PERC_FAT_BY_SKINFOLD !== $attr['calculate_perc_fat_by']) {
                    return null;
                }

                $can = $attr['skin_folds_formula'] === Avaliation::SKIN_FOLDS_FORMULA_7_FOLDS_JACKSON_POLLOCK;
                $can = $can || $attr['skin_folds_formula'] === Avaliation::SKIN_FOLDS_FORMULA_4_FOLDS_DURNIN_WOMERSLEY;

                if (!$can) {
                    return null;
                }

                return $this->faker->randomFloat(1, 0.4, 5);
            },
            'skin_folds_bicep_cm' => function(array $attr) {
                if (Avaliation::CALCULATE_PERC_FAT_BY_SKINFOLD !== $attr['calculate_perc_fat_by']) {
                    return null;
                }

                $can = $attr['skin_folds_formula'] === Avaliation::SKIN_FOLDS_FORMULA_4_FOLDS_DURNIN_WOMERSLEY;
                if (!$can) {
                    return null;
                }

                return $this->faker->randomFloat(1, 0.2, 4);
            },

            'client_notes' => function() {
                if (true === $this->faker->boolean()) {
                    return null;
                }
                return $this->faker->text(200);
            },
            'private_notes' => function() {
                if (true === $this->faker->boolean()) {
                    return null;
                }
                return $this->faker->text(200);
            },
            'revaluation_date' => function(array $attr) {
                if (true === $this->faker->boolean()) {
                    return null;
                }

                // get $sttr['date'] and add 30 days
                $date = \DateTime::createFromFormat('Y-m-d', $attr['date']);
                if ($date === false) {
                    return null;
                }

                $date->modify('+30 days');
                return $date->format('Y-m-d');
            },
            'photo_front_url' => function() {
                // TODO: Implement a way to generate a random image URL
                return null;
            },
            'photo_right_url' => function() {
                // TODO: Implement a way to generate a random image URL
                return null;
            },
            'photo_rear_url' => function() {
                // TODO: Implement a way to generate a random image URL
                return null;
            },
            'photo_left_url' => function() {
                // TODO: Implement a way to generate a random image URL
                return null;
            },
        ];
    }
}
