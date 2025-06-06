<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Helpers\Payments\MercadoPago;
use Illuminate\Support\Facades\Log;

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

    public function checkoutMessage(Request $request)
    {
        // http://127.0.0.1:8000/admin/subscription/checkoutMessage?preapproval_id=7214f32ae3cd4b7493683539711f4daf
        dd($request);
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
}
