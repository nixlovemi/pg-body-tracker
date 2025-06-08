<?php

namespace App\Helpers\Payments;

use MercadoPago\SDK;
use MercadoPago\Preference;
use MercadoPago\Item;
use MercadoPago\Preapproval;
use MercadoPago\Payment;
use App\Helpers\SysUtils;
use App\Helpers\Payments\SubscriptionTypes;
use App\Models\UserPlans;
use App\Helpers\Feature\FeatureAbstract;

class MercadoPago extends PaymentGatewayAbstract
{
    private bool $_isTest;
    private ?Payment $_payment = null;

    public function __construct()
    {
        $this->_isTest = env('APP_ENV') !== 'production';
        SDK::setAccessToken(
            $this->_isTest
                ? env('MERCADO_PAGO_TEST_ACCESS_TOKEN')
                : env('MERCADO_PAGO_ACCESS_TOKEN')
        );
    }

    public function extractSubscriptionDataFromWebhook(array $webhookData): ?PaymentGatewayDataAbstract
    {
        $PGData = new PaymentGatewayDataAbstract();
        $dataId = $webhookData['data']['id'] ?? null;

        // defaults
        if (isset($webhookData['type'])) {
            $PGData->setType($webhookData['type']);
        }

        if (isset($webhookData['date'])) {
            $PGData->setDate($webhookData['date']);
        } else if (isset($webhookData['date_created'])) {
            $PGData->setDate($webhookData['date_created']);
        }

        if (isset($webhookData['action'])) {
            $PGData->setAction($webhookData['action']);
        }

        if (isset($webhookData['status'])) {
            $PGData->setStatus($webhookData['status']);
        }

        // payment id
        if ($PGData->getType() === 'subscription_preapproval') {
            $PGData->setPaymentId($dataId);
        }

        if ($PGData->getType() === 'payment') {
            $payment = $this->getPaymentById($dataId);
            $subsId = $payment->subscription_id
                ?? ($payment->point_of_interaction->transaction_data->subscription_id ?? null);
            if ($subsId) {
                $PGData->setPaymentId($subsId);
            }
        }

        // TODO: there is no way to get the preapproval id from the webhook data when the type is 'subscription_authorized_payment'
        if ($PGData->getType() === 'subscription_authorized_payment') {
            $preapproval = $this->getPreapprovalById($dataId);
            if ($preapproval) {
                $PGData->setPaymentId($preapproval->id);
            }
        }

        return $PGData;
    }

    public function subscribe(string $plan): ?string
    {
        if (!in_array($plan, SubscriptionTypes::getPlans())) {
            // TODO: Log the error or handle it as needed
            return null;
        }

        $frequency = SubscriptionTypes::getPlanFrequency($plan);
        $frequencyType = SubscriptionTypes::getPlanFrequencyType($plan);
        $price = SubscriptionTypes::getPlanPrice($plan);
        $startDate = SysUtils::applyTimezone(now())->addMinutes(3);
        $endDate = SysUtils::applyTimezone(now())->add($frequencyType, $frequency);

        $preapproval = new Preapproval();
        $preapproval->back_url = $this->getCheckoutMessageUrl();
        $preapproval->reason = __('messages.pages.premium.subscription') . ': ' . SubscriptionTypes::getPlanLabel($plan);
        $preapproval->auto_recurring = [
            "frequency" => $frequency,
            "frequency_type" => $frequencyType,
            "transaction_amount" => $price,
            "currency_id" => "BRL",
            "start_date" => $startDate->format('Y-m-d\TH:i:s.000\Z'),
            "end_date" => $endDate->format('Y-m-d\TH:i:s.000\Z'),
        ];
        $preapproval->payer_email = $this->getPayerEmail();
        $preapproval->save();

        $preapprovalArray = $preapproval->toArray();
        if (empty($preapprovalArray['init_point']) || empty($preapprovalArray['id'])) {
            // TODO: Log the error or handle it as needed
            return null;
        }

        $retUserPlan = UserPlans::fSave([
            'user_id' => SysUtils::getLoggedInUser()?->id,
            'plan_type' => FeatureAbstract::FEATURE_PLAN_TYPE_PREMIUM,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'payment_data' => json_encode($preapprovalArray),
            'status' => UserPlans::STATUS_PENDING,
        ]);
        if ($retUserPlan->isError()) {
            // TODO: Log the error or handle it as needed
            return null;
        }

        $UserPlan = $retUserPlan->getValueFromResponse('UserPlans');
        $retPlanLog = $UserPlan->addLog([
            'payment_class' => self::class,
            'payment_id' => $preapprovalArray['id'],
            'data' => json_encode($preapprovalArray),
        ]);
        if ($retPlanLog->isError()) {
            // TODO: Log the error or handle it as needed
            return null;
        }

        return $preapproval->init_point;
    }

    public function getPayerEmail(): string
    {
        return $this->_isTest
            ? env('MERCADO_PAGO_TEST_BUYER_EMAIL')
            : (SysUtils::getLoggedInUser()?->email ?? '');
    }

    public function getCheckoutMessageUrl(): string
    {
        return $this->forceHttps(route('app.subscription.mercadoPagoCheckoutMessage'));
    }

    public function isPaymentApproved(UserPlans $UserPlan): bool
    {
        if ($UserPlan->status !== UserPlans::STATUS_PENDING) {
            return false;
        }

        $Payment = $this->getPaymentData($UserPlan);
        return $Payment?->status === 'approved' && $Payment?->status_detail === 'accredited';
    }

    public function isPaymentRejected(UserPlans $UserPlan): bool
    {
        if ($UserPlan->status !== UserPlans::STATUS_PENDING) {
            return false;
        }

        $Payment = $this->getPaymentData($UserPlan);
        return in_array($Payment?->status, ['rejected', 'cancelled', 'refunded', 'charged_back']);
    }

    public function getPaymentData(UserPlans $UserPlan): ?object
    {
        if ($UserPlan->status !== UserPlans::STATUS_PENDING) {
            return null;
        }

        // get log for the user plan where data json has type='payment'
        $userPlanLog = $UserPlan->logs()
            ->where('data', 'like', '%"type":"payment"%')
            ->orderBy('created_at', 'desc')
            ->first();
        if (!$userPlanLog) {
            return null;
        }

        $paymentClass = $userPlanLog->payment_class;
        if (!class_exists($paymentClass)) {
            return null;
        }

        $logData = json_decode($userPlanLog->data, true);
        $paymentId = $logData['data_id'] ?? null;

        return (new $paymentClass())->getPaymentById($paymentId);
    }

    // CUSTOM FUNCTIONS
    public function getPaymentById($paymentId): ?Payment
    {
        if (null !== $this->_payment) {
            return $this->_payment;
        }

        $this->_payment = Payment::find_by_id($paymentId);
        return $this->_payment;
    }

    public function getPreapprovalById($preapprovalId): ?Preapproval
    {
        return Preapproval::find_by_id($preapprovalId);
    }

    private function forceHttps(string $url): string
    {
        return preg_replace('/^http:/i', 'https:', $url);
    }
}
