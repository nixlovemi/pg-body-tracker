<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Helpers\Constants;
use App\Models\Client as mClient;
use App\Helpers\SysUtils;
use App\Helpers\ApiResponse;

class Client extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function index()
    {
        $user = SysUtils::getLoggedInUser();
        $clientCount = $user ? $user->getClientCount() : 0;

        return view('app.client.index', [
            'PAGE_TITLE' => __('messages.pages.client.index.title'),
            'CLIENT_COUNT' => $clientCount,
        ]);
    }

    public function add()
    {
        $user = SysUtils::getLoggedInUser();
        $isFirstClient = $user ? $user->isFirstClient() : false;

        return view('app.client.register', [
            'PAGE_TITLE' => __('messages.modalAddTitle', [
                'modelName' => __('messages.models.Client.name')
            ]),
            'TYPE' => Constants::FORM_ADD,
            'ACTION' => route('app.client.doSave'),
            'CLIENT' => null,
            'IS_FIRST_CLIENT' => $isFirstClient,
            'PREFILL_CLIENT' => [
                'first_name' => $user?->first_name,
                'last_name' => $user?->last_name,
            ],
        ]);
    }

    public function doSave(Request $request)
    {
        $form = $this->formatSaveRequest($request);
        $codedId = $form['cid'] ?? null;
        $user = SysUtils::getLoggedInUser();
        $hasNoClientsBeforeSave = $user ? $user->isFirstClient() : false;
        $response = mClient::fSave($form, $codedId);

        if ($response->isError()) {
            if ($codedId) {
                return $this->redirectToEditWithError($response, $codedId);
            }

            return $this->redirectToAddWithError($response);
        }

        $Client = $response->getValueFromResponse('Client');

        if (
            !$codedId
            && $hasNoClientsBeforeSave
            && '1' === (string) $request->input('f-onboarding-create-first-avaliation', '0')
        ) {
            return redirect()
                ->route('app.avaliation.index', [
                    'openAvaliation' => 1,
                    'openAvaliationCID' => $Client->codedId,
                ])
                ->withSuccess(__('messages.pages.client.register.selfShortcutSuccess'));
        }

        return redirect()
            ->route('app.client.edit', [
                'codedId' => $Client->codedId,
            ])
            ->withSuccess($response->getMessage());
    }

    public function edit(string $codedId)
    {
        $Client = mClient::getModelByCodedId($codedId);
        if (null === $Client) {
            return redirect()
                ->route('app.client.index')
                ->withErrors(['msg' => __('messages.saveModelNotFound', [
                    'modelName' => __('messages.models.Client.name')
                ])]);

        }

        // if its not your client, redirect to index
        if (!mClient::fHasAccess($Client)) {
            return redirect()
                ->route('app.client.index')
                ->withErrors(['msg' => __('messages.saveModelErrorSavingOther', [
                    'modelName' => __('messages.models.Client.name')
                ])]);
        }

        return view('app.client.register', [
            'PAGE_TITLE' => __('messages.modalEditTitle', [
                'modelName' => __('messages.models.Client.name')
            ]),
            'TYPE' => Constants::FORM_EDIT,
            'ACTION' => route('app.client.doSave'),
            'CLIENT' => $Client,
        ]);
    }

    public function view(string $codedId)
    {
        $Client = mClient::getModelByCodedId($codedId);
        if (null === $Client) {
            return redirect()
                ->route('app.client.index')
                ->withErrors(['msg' => __('messages.saveModelNotFound', [
                    'modelName' => __('messages.models.Client.name')
                ])]);

        }

        // if its not your client, redirect to index
        if (!mClient::fHasAccess($Client)) {
            return redirect()
                ->route('app.client.index')
                ->withErrors(['msg' => __('messages.saveModelErrorSavingOther', [
                    'modelName' => __('messages.models.Client.name')
                ])]);
        }

        return view('app.client.register', [
            'PAGE_TITLE' => __('messages.modalEditTitle', [
                'modelName' => __('messages.models.Client.name')
            ]),
            'TYPE' => Constants::FORM_VIEW,
            'ACTION' => '',
            'CLIENT' => $Client,
        ]);
    }

    private function formatSaveRequest(Request $request): array
    {
        $form = [];
        $form['cid'] = $request->input('f-cid') ?? null;
        $form['user_id'] = SysUtils::getLoggedInUser()?->id ?? null;
        $form['first_name'] = $request->input('f-name');
        $form['last_name'] = $request->input('f-surname');
        $form['email'] = $request->input('f-email');
        $form['phone'] = $request->input('f-phone');
        $form['gender'] = $request->input('f-bsex');
        $form['birthdate'] = $request->input('f-birth');
        $form['height_cm'] = $request->input('f-height') ?? 0;
        $form['weight_kg'] = $request->input('f-weight') ?? 0;

        // format birthdate from d/m/Y to Y-m-d
        if (null !== $form['birthdate']) {
            $form['birthdate'] = SysUtils::reformatDate($form['birthdate'], __('messages.dateFormat'), 'Y-m-d');
        }

        // format weight from kg
        $form['weight_kg'] = SysUtils::formatNumberToDb(
            $form['weight_kg'],
            1,
            __('messages.decimalSeparator'),
            __('messages.thousandSeparator')
        );

        return $form;
    }

    private function redirectToAddWithError(ApiResponse $response): \Illuminate\Http\RedirectResponse
    {
        return redirect()
            ->route('app.client.add')
            ->withInput()
            ->withErrors(['msg' => ApiResponse::getValidateMessage($response)]);
    }

    private function redirectToEditWithError(ApiResponse $response, string $codedId): \Illuminate\Http\RedirectResponse
    {
        return redirect()
            ->route('app.client.edit', ['codedId' => $codedId])
            ->withInput()
            ->withErrors(['msg' => ApiResponse::getValidateMessage($response)]);
    }
}
