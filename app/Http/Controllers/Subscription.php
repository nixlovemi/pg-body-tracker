<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Helpers\Payments\MercadoPago;
use Illuminate\Support\Facades\Log;
use App\Models\UserPlans;
use Symfony\Component\HttpFoundation\Response;

class Subscription extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function upgrade()
    {
        return view('app.subscription.upgrade', [
            'PAGE_TITLE' => __('messages.pages.premium.subscribe'),
        ]);
    }

    public function subscribe(string $plan)
    {
        $paymentClass = env('PAYMENT_CLASS');
        $url = (new $paymentClass())->subscribe($plan);
        if (!$url) {
            return $this->redirectWithError('app.subscription.upgrade', __('messages.pages.premium.subscriptionSaveError'));
        }

        return redirect($url);
    }

    public function mercadoPagoCheckoutMessage(Request $request)
    {
        $preapprovalId = $request->input('preapproval_id');
        $message = __('messages.pages.premium.defaultCheckoutMessage');
        $userPlanLog = \App\Models\UserPlanLogs::where('data', 'like', '%"'.$preapprovalId.'"%')->latest()->first();

        if ($userPlanLog) {
            $logData = json_decode($userPlanLog->data, true);
            $status = $logData['status'] ?? null;

            switch ($status) {
                case 'authorized':
                case 'pending':
                    $message = __('messages.pages.premium.processingMessage');
                    break;
                case 'active':
                    $message = __('messages.pages.premium.activeMessage');
                    break;
                case 'paused':
                case 'cancelled':
                    $message = __('messages.pages.premium.cancelledMessage');
                    break;
            }
        }

        return view('app.subscription.mercadoPagoCheckoutMessage', [
            'PAGE_TITLE' => __('messages.pages.premium.subscription'),
            'MESSAGE' => $message,
        ]);
    }

    public function mercadoPagoWebhook(Request $request)
    {
        // log it
        $form = $request->all();
        Log::info('Webhook Mercado Pago', $form);

        // proccess webhook
        try {
            MercadoPago::fProcessWebhookCall($form);
        } catch (\Throwable $e) {
            Log::error('Webhook Mercado Pago Error', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }

        return response()->json(['status' => 'processed'], 200);
    }

    public function details(Request $request)
    {
        $codedId = $request->input('codedId');
        $json = $request->input('json', '1');

        $UserPlan = $this->getUserPlanOrRedirect($codedId);
        if (($UserPlan instanceof UserPlans) === false) {
            return $UserPlan;
        }

        $paymentClass = $UserPlan->getPaymentClass() ?? '';
        $PaymentGateway = new ($paymentClass)();
        $paymentId = $UserPlan->getPaymentId();
        $preapprovalId = $UserPlan->getColPaymentId();
        $payment = $PaymentGateway->getPaymentById($paymentId);
        $preapproval = $PaymentGateway->getPreapprovalById($preapprovalId);

        if (empty($paymentId) || empty($preapprovalId)) {
            // this can happen when the user plan is created but the payment process was not completed, so we log it for debugging purposes
            Log::warning('Subscription details requested without payment identifiers', [
                'codedId' => $codedId,
                'user_plan_id' => $UserPlan->id,
                'payment_id' => $paymentId,
                'preapproval_id' => $preapprovalId,
            ]);
        }

        $view = view('app.subscription.modalDetails', [
            'TITLE' => __('messages.pages.premium.modalDetails.title', [
                'paymentId' => $UserPlan?->logs()->first()->getColIdString(),
            ]),
            'PAYMENT' => $payment,
            'PREAPPROVAL' => $preapproval,
            'USER_PLAN' => $UserPlan,
        ]);

        if (1 == $json) {
            return $this->returnResponse(
                false,
                __('messages.htmlReturned'),
                [
                    'html' => $view->render()
                ],
                Response::HTTP_OK
            );
        }

        return $view;
    }

    public function pauseSubscription(Request $request)
    {
        $codedId = $request->input('codedId');
        $UserPlan = $this->getUserPlanOrRedirect($codedId);
        if (($UserPlan instanceof UserPlans) === false) {
            return $UserPlan;
        }

        $ret = $UserPlan->pauseSubscription();
        if ($ret->isError()) {
            return $this->returnResponse(true, $ret->getMessage(), [], Response::HTTP_OK);
        }

        return $this->returnResponse(false, $ret->getMessage(), [], Response::HTTP_OK);
    }

    public function cancelSubscription(Request $request)
    {
        $codedId = $request->input('codedId');
        $UserPlan = $this->getUserPlanOrRedirect($codedId);
        if (($UserPlan instanceof UserPlans) === false) {
            return $UserPlan;
        }

        $ret = $UserPlan->cancelSubscription();
        if ($ret->isError()) {
            return $this->returnResponse(true, $ret->getMessage(), [], Response::HTTP_OK);
        }

        return $this->returnResponse(false, $ret->getMessage(), [], Response::HTTP_OK);
    }

    private function getUserPlanOrRedirect(string $codedId)
    {
        $UserPlan = UserPlans::getModelByCodedId($codedId);
        if (!$UserPlan || !UserPlans::fHasAccess($UserPlan)) {
            return $this->returnResponse(
                false,
                __('messages.modelErrorNoAccess'),
                [],
                Response::HTTP_UNAUTHORIZED
            );
        }

        return $UserPlan;
    }
}
