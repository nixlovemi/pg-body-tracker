<?php

namespace App\Helpers\Payments;

use App\Models\UserPlanLogs;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\UserPlans;
use App\Helpers\SysUtils;

abstract class PaymentGatewayAbstract implements PaymentGatewayInterface
{
    private static function fBuildWebhookAuditData(array $form, PaymentGatewayDataAbstract $PGData, ?string $resolvedStatus = null): array
    {
        $payload = $form;
        $payload['type'] = $PGData->getType();
        $payload['action'] = $PGData->getAction();
        $payload['data_id'] = $payload['data_id'] ?? ($payload['data']['id'] ?? null);

        if (!empty($resolvedStatus)) {
            $payload['status'] = $resolvedStatus;
        }

        $payload['audit_event'] = sprintf(
            '%s:%s:%s',
            $PGData->getType() ?: 'unknown_type',
            $PGData->getAction() ?: 'unknown_action',
            $resolvedStatus ?: 'unknown_status'
        );

        return $payload;
    }

    final public static function fProcessWebhookCall(array $form): void
    {
        $paymentClass = get_called_class();
        $Payment = new $paymentClass();

        Log::info('Webhook processing started', ['data_id' => $form['data']['id'] ?? null, 'type' => $form['type'] ?? null]);

        try {
            // vars
            $PGData = $Payment->extractSubscriptionDataFromWebhook($form);
            Log::info('Webhook data extracted successfully', ['data_id' => $form['data']['id'] ?? null, 'type' => $PGData->getType()]);

            // For subscription_authorized_payment: we can't resolve the preapproval_id from the webhook data_id
            // (data.id is an authorized_payment integer, not a preapproval UUID).
            // Strategy: find pending plans and sync their status directly using the preapproval_id stored in their logs.
            if ($PGData->getType() === 'subscription_authorized_payment') {
                $pendingPlans = UserPlans::where('status', UserPlans::STATUS_PENDING)
                    ->where('start_date', '<=', SysUtils::timezoneNow('Y-m-d'))
                    ->where('end_date', '>=', SysUtils::timezoneNow('Y-m-d'))
                    ->orderBy('start_date', 'asc')
                    ->limit(10)
                    ->get();

                // Also include active plans with past end_date (subscription renewals)
                $renewalPlans = UserPlans::where('status', UserPlans::STATUS_ACTIVE)
                    ->where('end_date', '<=', SysUtils::timezoneNow('Y-m-d'))
                    ->orderBy('end_date', 'asc')
                    ->limit(10)
                    ->get();

                $allPlans = $pendingPlans->merge($renewalPlans);
                Log::info('Processing subscription_authorized_payment', [
                    'data_id' => $form['data']['id'] ?? null,
                    'pending_plans' => $pendingPlans->count(),
                    'renewal_plans' => $renewalPlans->count(),
                ]);

                foreach ($allPlans as $UserPlan) {
                    Auth::onceUsingId($UserPlan->user->id);
                    // Sync using the preapproval_id already stored in the plan logs
                    $resolvedStatus = $Payment->syncSubscriptionStatus($UserPlan);
                    $auditStatus = $resolvedStatus ?? $UserPlan->status;
                    // Save the raw webhook event as a log entry for traceability
                    $UserPlan->addLog([
                        'payment_class' => $paymentClass,
                        'payment_id' => $UserPlan->getColPaymentId(),
                        'data' => json_encode(self::fBuildWebhookAuditData($form, $PGData, $auditStatus)),
                    ]);
                }
                return; // early return: no further log processing for this type
            }
        } catch (\Throwable $e) {
            Log::error('Error processing webhook', [
                'data_id' => $form['data']['id'] ?? null,
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            // Don't re-throw - we want to log the error and continue
            return;
        }

        // try to find the log
        $UserPlanLogs = UserPlanLogs::where('payment_class', $paymentClass)
            ->where('payment_id', $PGData->getPaymentId())
            ->orderBy('created_at', 'desc')
            ->first();
        if (!$UserPlanLogs) {
            Log::warning('Webhook: no UserPlanLogs found for payment_id', [
                'payment_class' => $paymentClass,
                'payment_id' => $PGData->getPaymentId(),
                'type' => $PGData->getType(),
                'data_id' => $form['data']['id'] ?? null,
            ]);
            return;
        }

        // log user just for this request
        Auth::onceUsingId($UserPlanLogs->userPlan->user_id);

        // check if exists any log from the same user_plan_id with same $date or if json field data.date is the same
        $UserPlanLogsCheck = UserPlanLogs::where('user_plan_id', $UserPlanLogs->user_plan_id)
            ->where('data->action', $PGData->getAction())
            ->where(function ($query) use ($PGData) {
                $query->whereDate('created_at', $PGData->getDate())
                    ->orWhere('data->date', $PGData->getDate())
                    ->orWhere('data->date_created', $PGData->getDate());
            })
            ->first();
        if (null !== $UserPlanLogsCheck) {
            // TODO: skip to avoid duplicate logs
            return;
        }

        $resolvedStatus = $Payment->syncSubscriptionStatus($UserPlanLogs->userPlan);
        $auditStatus = $resolvedStatus ?? $UserPlanLogs->userPlan->status;

        // add a new log entry
        $retPlanLog = $UserPlanLogs->userPlan->addLog([
            'payment_class' => $paymentClass,
            'payment_id' => $PGData->getPaymentId(),
            'data' => json_encode(self::fBuildWebhookAuditData($form, $PGData, $auditStatus)),
        ]);
        if ($retPlanLog->isError()) {
            // TODO: Log the error or handle it as needed
            return;
        }

        return;
    }
}
