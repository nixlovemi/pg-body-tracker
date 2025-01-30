<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Symfony\Component\HttpFoundation\Response;
use App\Helpers\Constants;
use App\Helpers\SysUtils;
use App\Helpers\ApiResponse;
use App\Models\Client;
use App\Models\Goal as mGoal;

class Goal extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function htmlModalAdd(Request $request)
    {
        $view = view('app.goal.modalRegister', [
            'CUID' => $request->input('cuid'),
            'CEDIT' => $request->input('cedit'),
            'ACTION' => route('app.goal.doModalAdd'),
        ]);

        if (1 == $request->input('json')) {
            return $this->returnResponse(
                false,
                'HTML retornado com sucesso!',
                [
                    'html' => $view->render()
                ],
                Response::HTTP_OK
            );
        }

        return $view;
    }

    public function doModalAdd(Request $request)
    {
        $form = $this->formatSaveRequest($request);
        $response = mGoal::fSave($form);
        if ($response->isError()) {
            return $this->returnResponse(true, ApiResponse::getValidateMessage($response), [], Response::HTTP_OK);
        }

        $htmlData = $this->getCardGoalContent($request);
        return $this->returnResponse(false, $response->getMessage(), [
            'html' => $htmlData,
        ], Response::HTTP_OK);
    }

    private function getCardGoalContent(Request $request): string
    {
        $Client = Client::getModelByCodedId($request->input('f-cid'));
        $canEdit = (1 == $request->input('f-cedit')) ? true: false;

        $view = view('app.client.partials.cardGoalsContent', [
            'CLIENT' => $Client,
            'CAN_EDIT' => $canEdit,
        ]);

        return $view->render();
    }

    private function formatSaveRequest(Request $request): array
    {
        $form = [];
        $form['cid'] = $request->input('f-cid') ?? null;
        $form['objective'] = $request->input('f-objective') ?? null;
        $form['target_weight_kg'] = $request->input('f-weight') ?? 0;
        $form['deadline'] = $request->input('f-deadline') ?? null;

        // get Client
        $Client = Client::getModelByCodedId($form['cid']);
        $form['client_id'] = $Client?->id;

        // format deadline from d/m/Y to Y-m-d
        if (null !== $form['deadline']) {
            $form['deadline'] = SysUtils::reformatDate($form['deadline'], __('messages.dateFormat'), 'Y-m-d');
        }

        // format target_weight_kg from kg
        $form['target_weight_kg'] = SysUtils::formatNumberToDb(
            $form['target_weight_kg'],
            3,
            __('messages.decimalSeparator'),
            __('messages.thousandSeparator')
        );

        return $form;
    }
}
