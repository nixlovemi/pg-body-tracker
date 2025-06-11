<?php

namespace App\Helpers\Payments;

use App\Models\UserPlanLogs;
use Illuminate\Support\Facades\Auth;
use App\Models\UserPlans;
use App\Helpers\SysUtils;

abstract class PaymentGatewayAbstract implements PaymentGatewayInterface
{
    final public static function fProcessWebhookCall(array $form): void
    {
        $paymentClass = get_called_class();
        $Payment = new $paymentClass();

        // vars
        $PGData = $Payment->extractSubscriptionDataFromWebhook($form);

        // currently, we can't get payment_id when the return is a subscription_authorized_payment (MercadoPago)
        // so, we will try to check the payment status using the body data_id
        if ($PGData->getType() === 'subscription_authorized_payment') {
            // get all user plans with status = 'pending'
            $UserPlans = UserPlans::where('status', UserPlans::STATUS_PENDING)
                ->where('start_date', '<=', SysUtils::timezoneNow('Y-m-d'))
                ->where('end_date', '>=', SysUtils::timezoneNow('Y-m-d'))
                ->orderBy('start_date', 'asc')
                ->limit(10)
                ->get();
            foreach ($UserPlans as $UserPlan) {
                // log user just for this request
                Auth::onceUsingId($UserPlan->user->id);
                $UserPlan->user->checkPlanPaymentStatus();
            }

            return;
        }

        // try to find the log
        $UserPlanLogs = UserPlanLogs::where('payment_class', $paymentClass)
            ->where('payment_id', $PGData->getPaymentId())
            ->orderBy('created_at', 'desc')
            ->first();
        if (!$UserPlanLogs) {
            // TODO: Log the error or handle it as needed
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

        // add a new log entry
        $retPlanLog = $UserPlanLogs->userPlan->addLog([
            'payment_class' => $paymentClass,
            'payment_id' => $PGData->getPaymentId(),
            'data' => json_encode($form),
        ]);
        if ($retPlanLog->isError()) {
            // TODO: Log the error or handle it as needed
            return;
        }

        return;
    }
}
