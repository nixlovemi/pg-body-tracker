<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Symfony\Component\HttpFoundation\Response;
use App\Helpers\SysUtils;
use App\Helpers\ApiResponse;
use App\Models\Client;
use App\Models\Avaliation as mAvaliation;

class Avaliation extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function htmlModalAdd(Request $request)
    {
        $view = view('app.avaliation.modalRegister', [
            'CUID' => $request->input('cuid'),
            'CEDIT' => $request->input('cedit'),
            'ACTION' => route('app.avaliation.doModalAdd'),
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
        $response = mAvaliation::fSave($form, $form['acid']);

        if ($response->isError()) {
            return $this->returnResponse(true, ApiResponse::getValidateMessage($response), [], Response::HTTP_OK);
        }

        return $this->returnResponse(false, $response->getMessage(), [], Response::HTTP_OK);
    }

    public function htmlModalView(Request $request)
    {
        $Avaliation = mAvaliation::getModelByCodedId($request->input('codedId'));
        $view = view('app.avaliation.modalRegister', [
            'CUID' => $Avaliation?->client->codedId,
            'CEDIT' => 0,
            'ACTION' => null,
            'AVALIATION' => $Avaliation,
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

    public function htmlModalEdit(Request $request)
    {
        $Avaliation = mAvaliation::getModelByCodedId($request->input('codedId'));
        $view = view('app.avaliation.modalRegister', [
            'CUID' => $Avaliation?->client->codedId,
            'CEDIT' => 1,
            'ACTION' => route('app.avaliation.doModalAdd'),
            'AVALIATION' => $Avaliation,
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

    private function formatSaveRequest(Request $request): array
    {
        $form = [];
        $form['cid'] = $request->input('f-cid') ?? null;
        $form['acid'] = $request->input('f-acid') ?? null;
        $form['date'] = $request->input('f-date') ?? null;
        $form['weight_kg'] = $request->input('f-weight') ?? null;
        $form['body_fat_perc'] = $request->input('f-bfat') ?? null;
        $form['skeletal_muscle_perc'] = $request->input('f-skeletal_mp') ?? 0;
        $form['visceral_fat_kg'] = $request->input('f-visceral_fat') ?? 0;
        $form['waist_circumference_cm'] = $request->input('f-waist_circ') ?? 0;

        // get Client
        $Client = Client::getModelByCodedId($form['cid']);
        $form['client_id'] = $Client?->id;

        // format deadline from d/m/Y to Y-m-d
        if (null !== $form['date']) {
            $form['date'] = SysUtils::reformatDate($form['date'], __('messages.dateFormat'), 'Y-m-d');
        }

        // format number to db
        foreach ([
            'weight_kg',
            'body_fat_perc',
            'skeletal_muscle_perc',
            'visceral_fat_kg',
            'waist_circumference_cm',
        ] as $field) {
            if (null === $form[$field]) {
                continue;
            }

            $form[$field] = SysUtils::formatNumberToDb(
                $form[$field],
                3,
                __('messages.decimalSeparator'),
                __('messages.thousandSeparator')
            );
        }

        return $form;
    }
}
