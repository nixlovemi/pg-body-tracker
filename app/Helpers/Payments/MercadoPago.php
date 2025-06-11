<?php

namespace App\Helpers\Payments;

use MercadoPago\SDK;
use MercadoPago\Preapproval;
use MercadoPago\Payment;
use App\Helpers\SysUtils;
use App\Helpers\Payments\SubscriptionTypes;
use App\Models\UserPlans;
use App\Helpers\Feature\FeatureAbstract;
use Carbon\Carbon;

class MercadoPago extends PaymentGatewayAbstract
{
    private bool $_isTest;
    private ?Payment $_payment = null;

    public const PRE_APPROVAL_STATUS_AUTHORIZED = 'authorized';
    public const PRE_APPROVAL_STATUS_PAUSED = 'paused';
    public const PRE_APPROVAL_STATUS_CANCELLED = 'cancelled';
    public const PRE_APPROVAL_STATUS_PENDING = 'pending';

    public const PAYMENT_STATUS_APPROVED = 'approved';
    public const PAYMENT_STATUS_PENDING = 'pending';
    public const PAYMENT_STATUS_AUTHORIZED = 'authorized';
    public const PAYMENT_STATUS_IN_PROCESS = 'in_process';
    public const PAYMENT_STATUS_IN_MEDIATION = 'in_mediation';
    public const PAYMENT_STATUS_REJECTED = 'rejected';
    public const PAYMENT_STATUS_CANCELLED = 'cancelled';
    public const PAYMENT_STATUS_REFUNDED = 'refunded';
    public const PAYMENT_STATUS_CHARGED_BACK = 'charged_back';

    public const PAYMENT_STATUS_DETAIL_ACCREDITED = 'accredited';
    public const PAYMENT_STATUS_DETAIL_PENDING_CONTINGENCY = 'pending_contingency';
    public const PAYMENT_STATUS_DETAIL_PENDING_REVIEW_MANUAL = 'pending_review_manual';
    public const PAYMENT_STATUS_DETAIL_CC_REJECTED_BAD_FILLED_CARD_NUMBER = 'cc_rejected_bad_filled_card_number';
    public const PAYMENT_STATUS_DETAIL_CC_REJECTED_INSUFFICIENT_AMOUNT = 'cc_rejected_insufficient_amount';
    public const PAYMENT_STATUS_DETAIL_CC_REJECTED_CALL_FOR_AUTHORIZE = 'cc_rejected_call_for_authorize';
    public const PAYMENT_STATUS_DETAIL_CC_REJECTED_CARD_DISABLED = 'cc_rejected_card_disabled';
    public const PAYMENT_STATUS_DETAIL_CC_REJECTED_CARD_ERROR = 'cc_rejected_card_error';
    public const PAYMENT_STATUS_DETAIL_CC_REJECTED_OTHER_REASON = 'cc_rejected_other_reason';

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

    public function pauseSubscription(UserPlans $UserPlan): bool
    {
        try {
            $preapprovalId = $UserPlan->getColPaymentId();
            $preapproval = $this->getPreapprovalById($preapprovalId);

            if (!$preapproval || $preapproval->status !== self::PRE_APPROVAL_STATUS_AUTHORIZED) {
                return false;
            }

            // Envie a requisição PUT para pausar a assinatura
            $updatedPreapproval = $preapproval->update([
                'status' => self::PRE_APPROVAL_STATUS_PAUSED
            ]);

            // Confirma que o status retornado é "paused"
            if ($updatedPreapproval && $updatedPreapproval->status === self::PRE_APPROVAL_STATUS_PAUSED) {
                return true;
            }

            // Algo não esperado
            return false;
        } catch (\Exception $e) {
            // TODO: Log the error or handle it as needed
            return false;
        }
    }

    public function cancelSubscription(UserPlans $UserPlan): bool
    {
        try {
            $preapprovalId = $UserPlan->getColPaymentId();
            $preapproval = $this->getPreapprovalById($preapprovalId);

            if (!$preapproval || $preapproval->status === self::PRE_APPROVAL_STATUS_CANCELLED) {
                return false;
            }

            // Envie a requisição PUT para cancelar a assinatura
            $updatedPreapproval = $preapproval->update([
                'status' => self::PRE_APPROVAL_STATUS_CANCELLED
            ]);

            // Confirma que o status retornado é "cancelled"
            if ($updatedPreapproval && $updatedPreapproval->status === self::PRE_APPROVAL_STATUS_CANCELLED) {
                return true;
            }

            // Algo não esperado
            return false;
        } catch (\Exception $e) {
            // TODO: Log the error or handle it as needed
            return false;
        }
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
        $Payment = $this->getPaymentData($UserPlan);
        return $Payment?->status === self::PAYMENT_STATUS_APPROVED && $Payment?->status_detail === self::PAYMENT_STATUS_DETAIL_ACCREDITED;
    }

    public function isPaymentRejected(UserPlans $UserPlan): bool
    {
        $Payment = $this->getPaymentData($UserPlan);
        return in_array($Payment?->status, [self::PAYMENT_STATUS_REJECTED, self::PAYMENT_STATUS_CANCELLED, self::PAYMENT_STATUS_REFUNDED, self::PAYMENT_STATUS_CHARGED_BACK]);
    }

    public function getPaymentData(UserPlans $UserPlan): ?object
    {
        $paymentClass = $UserPlan->getPaymentClass();
        if (!class_exists($paymentClass)) {
            return null;
        }

        $paymentId = $UserPlan->getPaymentId() ?? null;
        if (null === $paymentId) {
            return null;
        }
        return (new $paymentClass())->getPaymentById($paymentId);
    }

    public function syncSubscriptionStatus(UserPlans $UserPlan): ?string
    {
        try {
            $preapprovalId = $UserPlan->getColPaymentId();
            $preapproval = $this->getPreapprovalById($preapprovalId);

            if (!$preapproval) {
                // Não conseguiu buscar a assinatura
                return null;
            }

            // Mapeia o status do Preapproval para o UserPlans
            $mappedStatus = $this->mapPreapprovalStatusToUserPlanStatus($preapproval->status);

            // when type is payment, we need to check the new date period
            if (self::PRE_APPROVAL_STATUS_AUTHORIZED === $preapproval->status) {
                $this->syncCheckForNewPaymentDate($UserPlan);
            }

            // Se o status atual já for o mesmo, não precisa atualizar
            if ($mappedStatus === $UserPlan->status) {
                return null;
            }

            // Atualiza e salva
            $UserPlan->status = $mappedStatus;
            $UserPlan->save();

            // TODO: Opcional: você pode logar isso ou adicionar um log no UserPlanLog se quiser
            return $mappedStatus;
        } catch (\Exception $e) {
            // TODO: logar o erro se quiser
            return null;
        }
    }

    // CUSTOM FUNCTIONS
    // TODO: make this generic as not all payment gateways will have getPaymentById or getPreapprovalById methods
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

    /**
     * Traduz status de Payment
     */
    public function getPaymentStatusLabel(?string $status): string
    {
        $map = [
            self::PAYMENT_STATUS_APPROVED => __('messages.pages.premium.paymentStatusApproved'),
            self::PAYMENT_STATUS_PENDING => __('messages.pages.premium.paymentStatusPending'),
            self::PAYMENT_STATUS_AUTHORIZED => __('messages.pages.premium.paymentStatusAuthorized'),
            self::PAYMENT_STATUS_IN_PROCESS => __('messages.pages.premium.paymentStatusInProcess'),
            self::PAYMENT_STATUS_IN_MEDIATION => __('messages.pages.premium.paymentStatusInMediation'),
            self::PAYMENT_STATUS_REJECTED => __('messages.pages.premium.paymentStatusRejected'),
            self::PAYMENT_STATUS_CANCELLED => __('messages.pages.premium.paymentStatusCancelled'),
            self::PAYMENT_STATUS_REFUNDED => __('messages.pages.premium.paymentStatusRefunded'),
            self::PAYMENT_STATUS_CHARGED_BACK => __('messages.pages.premium.paymentStatusChargedBack'),
        ];

        return $map[$status] ?? ucfirst(str_replace('_', ' ', $status));
    }

    /**
     * Traduz status_detail de Payment
     */
    public function getPaymentStatusDetailLabel(?string $statusDetail): string
    {
        $map = [
            self::PAYMENT_STATUS_DETAIL_ACCREDITED => __('messages.pages.premium.paymentStatusDetailAccredited'),
            self::PAYMENT_STATUS_DETAIL_PENDING_CONTINGENCY => __('messages.pages.premium.paymentStatusDetailPendingContingency'),
            self::PAYMENT_STATUS_DETAIL_PENDING_REVIEW_MANUAL => __('messages.pages.premium.paymentStatusDetailPendingReviewManual'),
            self::PAYMENT_STATUS_DETAIL_CC_REJECTED_BAD_FILLED_CARD_NUMBER => __('messages.pages.premium.paymentStatusDetailCcRejectedBadFilledCardNumber'),
            self::PAYMENT_STATUS_DETAIL_CC_REJECTED_INSUFFICIENT_AMOUNT => __('messages.pages.premium.paymentStatusDetailCcRejectedInsufficientAmount'),
            self::PAYMENT_STATUS_DETAIL_CC_REJECTED_CALL_FOR_AUTHORIZE => __('messages.pages.premium.paymentStatusDetailCcRejectedCallForAuthorize'),
            self::PAYMENT_STATUS_DETAIL_CC_REJECTED_CARD_DISABLED => __('messages.pages.premium.paymentStatusDetailCcRejectedCardDisabled'),
            self::PAYMENT_STATUS_DETAIL_CC_REJECTED_CARD_ERROR => __('messages.pages.premium.paymentStatusDetailCcRejectedCardError'),
            self::PAYMENT_STATUS_DETAIL_CC_REJECTED_OTHER_REASON => __('messages.pages.premium.paymentStatusDetailCcRejectedOtherReason'),
        ];

        return $map[$statusDetail] ?? ucfirst(str_replace('_', ' ', $statusDetail));
    }

    /**
     * Traduz status da assinatura (Preapproval)
     */
    public function getPreapprovalStatusLabel(?string $status): string
    {
        $map = [
            self::PRE_APPROVAL_STATUS_AUTHORIZED => __('messages.pages.premium.preApprovalStatusAuthorized'),
            self::PRE_APPROVAL_STATUS_PAUSED => __('messages.pages.premium.preApprovalStatusPaused'),
            self::PRE_APPROVAL_STATUS_CANCELLED => __('messages.pages.premium.preApprovalStatusCancelled'),
            self::PRE_APPROVAL_STATUS_PENDING => __('messages.pages.premium.preApprovalStatusPending'),
        ];

        return $map[$status] ?? ucfirst(str_replace('_', ' ', $status));
    }

    public function getPaymentMethodLabel(?string $paymentMethodId): string
    {
        $map = [
            'account_money' => __('messages.pages.premium.mercadoPagoPaymentAccountMoney'),
            'credit_card' => __('messages.pages.premium.mercadoPagoPaymentCreditCard'),
            'debit_card' => __('messages.pages.premium.mercadoPagoPaymentDebitCard'),
            'ticket' => __('messages.pages.premium.mercadoPagoPaymentTicket'),
            'bank_transfer' => __('messages.pages.premium.mercadoPagoPaymentBankTransfer'),
            'pix' => __('messages.pages.premium.mercadoPagoPaymentPix'),
            'prepaid_card' => __('messages.pages.premium.mercadoPagoPaymentPrepaidCard'),
        ];

        return $map[$paymentMethodId] ?? ucfirst(str_replace('_', ' ', $paymentMethodId));
    }

    private function mapPreapprovalStatusToUserPlanStatus(string $preapprovalStatus): string
    {
        switch ($preapprovalStatus) {
            case self::PRE_APPROVAL_STATUS_AUTHORIZED:
                return UserPlans::STATUS_ACTIVE;

            case self::PRE_APPROVAL_STATUS_PAUSED:
                return UserPlans::STATUS_PAUSED;

            case self::PRE_APPROVAL_STATUS_CANCELLED:
                return UserPlans::STATUS_CANCELED;

            default:
                return UserPlans::STATUS_PENDING;
        }
    }

    private function syncCheckForNewPaymentDate(UserPlans &$UserPlan): void
    {
        $lastLog = $UserPlan->getLastLogRow();
        $lastLogData = json_decode($lastLog?->data ?? '{}', true);
        if (false === self::isPaymentLog($lastLogData)) {
            return;
        }

        $paymentClass = $UserPlan->getPaymentClass();
        $Payment = (new $paymentClass())->getPaymentById($lastLogData['data_id'] ?? '');
        if (!$Payment || self::PAYMENT_STATUS_APPROVED !== $Payment->status) {
            return;
        }

        $pointOfInteraction = $Payment->point_of_interaction ?? null;
        if (null === $pointOfInteraction) {
            return;
        }

        $transactionData = $pointOfInteraction->transaction_data ?? null;
        if (null === $transactionData) {
            return;
        }

        $billingDate = $transactionData->billing_date ?? null;
        $period = $transactionData->invoice_period->period ?? null;
        $type = $transactionData->invoice_period->type ?? null;
        if (null === $billingDate || null === $period || null === $type) {
            return;
        }

        // change the UserPlan dates
        $newEndDate = $this->calculateNewEndDate($billingDate, $period, $type);
        if (!$newEndDate) {
            return;
        }

        // Update the UserPlan with the new end date
        $UserPlan->renewSubscription($newEndDate);
    }

    public static function isPaymentLog(array $logData): bool
    {
        return (isset($logData['type']) && $logData['type'] === 'payment') ||
            (isset($logData['action']) && in_array($logData['action'], [
                'payment.created',
                'subscription_authorized_payment',
                'subscription_authorized_payment.created'
            ]));
    }

    private function calculateNewEndDate(string $billingDate, int $period, string $type): ?Carbon
    {
        if (!$billingDate || !$period || !$type) {
            return null;
        }

        $frequencyType = SubscriptionTypes::getPlanFrequencyType($type);
        return SysUtils::applyTimezone($billingDate)->add($frequencyType, $period);
    }
}
