<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\UserPlans;

class UserPlanLogsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // TODO: improve this factory to use real payment classes and IDs
        return [
            'user_plan_id' => function() {
                return UserPlans::inRandomOrder()
                    ->first();
            },
            'payment_class' => $this->faker->randomElement(['App\Helpers\Payments\MercadoPago']),
            'payment_id' => "eaebcc0d0698463592f3610d0c48c74e",
            'data' => json_encode([
                "id" => "eaebcc0d0698463592f3610d0c48c74e",
                "payer_id" => 2477134675,
                "back_url" => "https://127.0.0.1:8000/admin/subscription/checkoutMessage",
                "collector_id" => 2477115845,
                "application_id" => 3972720863774953,
                "status" => UserPlans::STATUS_PENDING,
                "auto_recurring" => [],
                "init_point" => "https://www.mercadopago.com.br/subscriptions/checkout?preapproval_id=eaebcc0d0698463592f3610d0c48c74e",
                "reason" => "Assinatura: Mensal",
                "date_created" => "2025-06-06T00:02:46.188-04:00",
                "last_modified" => "2025-06-06T00:02:46.778-04:00",
                "next_payment_date" => "2025-06-06T00:02:46.000-04:00",
                "summarized" => [],
                "subscription_id" => "eaebcc0d0698463592f3610d0c48c74e",
            ]),
        ];
    }
}
