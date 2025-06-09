<?php

namespace App\Helpers\Payments;

use App\Models\UserPlans;

interface PaymentGatewayInterface
{
    public function extractSubscriptionDataFromWebhook(array $webhookData): ?PaymentGatewayDataAbstract;

    public function subscribe(string $plan): ?string; // returns the redirect_url to continue the subscription process

    public function pauseSubscription(UserPlans $UserPlan): bool;

    public function cancelSubscription(UserPlans $UserPlan): bool;

    public function getPayerEmail(): string;

    public function getCheckoutMessageUrl(): string;

    public static function fProcessWebhookCall(array $form): void;

    public function isPaymentApproved(UserPlans $UserPlan): bool;

    public function isPaymentRejected(UserPlans $UserPlan): bool;

    public function getPaymentData(UserPlans $UserPlan): ?object;

    public function syncSubscriptionStatus(UserPlans $UserPlan): ?string;
}
