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
        MercadoPago::fProcessWebhookCall($form);

        return response()->json(['status' => 'processed'], 200);

        /*
        // Valida se é tipo assinatura
        if ($request->input('type') !== 'subscription_preapproval') {
            return response()->json(['status' => 'ignored'], 200);
        }

        // Captura o ID da assinatura
        $preapprovalId = $request->input('data.id');

        // Inicializa SDK
        $mercadoPago = new MercadoPago();

        // Tenta buscar detalhes da assinatura
        try {
            $preapproval = $mercadoPago->findPreapprovalById($preapprovalId);

            // Loga o retorno da assinatura
            Log::info('Dados da assinatura', $preapproval->toArray());
            dd($preapproval->toArray());

            // Aqui você pode:
            // - Atualizar o plano do usuário
            // - Verificar status
            // - Salvar informações como next_payment_date, status, etc.

            // Exemplo de extração de dados
            $status = $preapproval->status; // authorized, paused, cancelled
            $payerEmail = $preapproval->payer_email;
            $startDate = $preapproval->auto_recurring['start_date'] ?? null;
            $nextPaymentDate = $preapproval->next_payment_date ?? null;

            // Sua lógica de negócio aqui...

            return response()->json(['status' => 'processed'], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar assinatura MP', ['message' => $e->getMessage()]);
            return response()->json(['status' => 'error'], 500);
        }
        */
    }

    public function details(Request $request)
    {
        $codedId = $request->input('codedId');
        $json = $request->input('json', '1');

        $UserPlan = UserPlans::getModelByCodedId($codedId);
        if (!$UserPlan || !UserPlans::fHasAccess($UserPlan)) {
            return $this->returnResponse(
                false,
                __('messages.modelErrorNoAccess'),
                [],
                Response::HTTP_UNAUTHORIZED
            );
        }

        $paymentClass = $UserPlan->getPaymentClass() ?? '';
        $PaymentGateway = new ($paymentClass)();
        $payment = $PaymentGateway->getPaymentById($UserPlan->getPaymentId());
        $preapproval = $PaymentGateway->getPreapprovalById($UserPlan->getColPaymentId());

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
}
