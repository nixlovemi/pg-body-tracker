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
use Barryvdh\DomPDF\Facade\Pdf;
use App\Helpers\Constants;
use Illuminate\Support\Facades\URL;
use App\Helpers\Feature\RevaluationDate;
use App\Services\AvaliationPdfCacheService;
use App\Jobs\GenerateAvaliationPdfCacheJob;

class Avaliation extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    private const SEND_WHATS_HOURS = 168; // 7 days
    private const SEND_MAIL_HOURS = self::SEND_WHATS_HOURS; // 7 days

    public function index()
    {
        $user = SysUtils::getLoggedInUser();
        $clientCount = $user ? $user->getClientCount() : 0;
        $avaliationCount = $user ? $user->getAvaliationCount() : 0;

        return view('app.avaliation.index', [
            'PAGE_TITLE' => __('messages.pages.avaliation.index.title'),
            'CLIENT_COUNT' => $clientCount,
            'AVALIATION_COUNT' => $avaliationCount,
        ]);
    }

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
                __('messages.htmlReturned'),
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

        $Avaliation = $response->getValueFromResponse('Avaliation');
        if ($Avaliation) {
            $this->saveAvaliationsPhotos($Avaliation, $request);

            // True async pre-generation: enqueue job and return immediately.
            GenerateAvaliationPdfCacheJob::dispatch($Avaliation->id, true, true)
                ->onConnection('database')
                ->onQueue('pdf');
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
                __('messages.htmlReturned'),
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
                __('messages.htmlReturned'),
                [
                    'html' => $view->render()
                ],
                Response::HTTP_OK
            );
        }

        return $view;
    }

    public function htmlModalSelectClient(Request $request)
    {
        $view = view('app.avaliation.modalSelectClient', []);

        if (1 == $request->input('json')) {
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

    public function showPhoto(string $fileName)
    {
        // TODO: similar to Avaliation->getPhotoBase64(string $fieldName)???
        $path = storage_path(mAvaliation::fGetOsPhotosFolder(mAvaliation::BASE_PHOTOS_FOLDER) . DIRECTORY_SEPARATOR . $fileName);

        if (!file_exists($path)) {
            abort(404);
        }

        return response()->file($path);
    }

    public function viewReport(string $codedId)
    {
        $Avaliation = mAvaliation::getModelByCodedId($codedId);
        if (null === $Avaliation) {
            return $this->redirectWithError('app.client.index', __('messages.modelErrorNoAccess'));
        }

        return view('app.avaliation.viewReport', [
            'PAGE_TITLE' => __('messages.pages.avaliation.index.title'),
            'AVALIATION' => $Avaliation,
        ]);
    }

    public function viewReportPDF(Request $request, string $codedId)
    {
        $Avaliation = mAvaliation::getModelByCodedId($codedId);
        if (null === $Avaliation) {
            return $this->redirectWithError('app.client.index', __('messages.modelErrorNoAccess'));
        }

        $includeGraphs = filter_var($request->query('graphs', '1'), FILTER_VALIDATE_BOOLEAN);
        $includePictures = filter_var($request->query('pictures', '1'), FILTER_VALIDATE_BOOLEAN);

        $pdfCacheService = app(AvaliationPdfCacheService::class);

        $existingCache = $pdfCacheService->getCurrentSnapshotCache($Avaliation, $includeGraphs, $includePictures);
        if ($pdfCacheService->isReadyCache($existingCache)) {
            return response()->file($pdfCacheService->absolutePath($existingCache->storage_path), [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="avaliation-' . $Avaliation->codedId . '.pdf"',
            ]);
        }

        // Ensure legacy/no-hash cases create a cache record on first PDF open.
        $cache = $pdfCacheService->ensurePendingCacheRecord($Avaliation, $includeGraphs, $includePictures);

        // Enqueue async generation if file is not ready yet.
        GenerateAvaliationPdfCacheJob::dispatch($Avaliation->id, $includeGraphs, $includePictures)
            ->onConnection('database')
            ->onQueue('pdf');

        if ($request->query('sync') === '1') {
            // Optional fallback for manual troubleshooting.
            $syncCache = $pdfCacheService->ensureCurrentPdfCached($Avaliation, $includeGraphs, $includePictures);
            if ($syncCache !== null && !empty($syncCache->storage_path)) {
                return response()->file($pdfCacheService->absolutePath($syncCache->storage_path), [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="avaliation-' . $Avaliation->codedId . '.pdf"',
                ]);
            }
        }

        $syncUrl = $request->fullUrlWithQuery(['sync' => 1]);

        $waitHtml = sprintf(
            '<!doctype html><html><head><meta charset="utf-8"><meta http-equiv="refresh" content="3">'
            . '<title>Gerando PDF</title><style>body{font-family:Arial,sans-serif;padding:24px;line-height:1.5;} .muted{color:#666;}</style></head><body>'
            . '<h3>Estamos gerando o PDF, esse processo pode demorar um pouco.</h3>'
            . '<p><a href="%s">Se quiser, clique aqui para forçar a criação do PDF.</a></p>'
            . '<p class="muted">A página vai atualizar automaticamente em 3 segundos.</p>'
            . '</body></html>',
            e($syncUrl)
        );

        return response($waitHtml, 202)
            ->header('Content-Type', 'text/html; charset=UTF-8')
            ->header('Retry-After', '3');
    }

    public function htmlModalSendWhats(Request $request)
    {
        $Avaliation = mAvaliation::getModelByCodedId($request->input('cid', ''));
        if (null === $Avaliation) {
            return $this->redirectWithError('app.avaliation.index', __('messages.modelErrorNoAccess'));
        }

        $view = view('app.avaliation.modalSendWhats', [
            'AVALIATION' => $Avaliation,
        ]);

        if (1 == $request->input('json', 0)) {
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

    public function doModalSendWhats(Request $request)
    {
        $Avaliation = mAvaliation::getModelByCodedId($request->input('cid', ''));
        if (!$Avaliation) {
            return $this->modelNotFoundResponse();
        }

        $code = $request->input('country_code', '');
        $phone = preg_replace('/[^0-9]/', '', $code . $request->input('phone', ''));

        $link = $this->getCachedAvaliationLink($Avaliation, 'whats', $phone, self::SEND_WHATS_HOURS);

        $message = __('messages.pages.avaliation.modalSendWhats.whatsMessage', [
            'clientName' => $Avaliation->client->getName(),
            'link' => $link,
        ]);
        $url = sprintf(Constants::WHATS_LINK_URL, $phone, urlencode($message));

        return $this->returnResponse(
            false,
            __('messages.pages.avaliation.modalSendWhats.successMessage'),
            ['url' => $url],
            Response::HTTP_OK
        );
    }

    public function htmlModalSendMail(Request $request)
    {
        $Avaliation = mAvaliation::getModelByCodedId($request->input('cid', ''));
        if (null === $Avaliation) {
            return $this->redirectWithError('app.avaliation.index', __('messages.modelErrorNoAccess'));
        }

        $view = view('app.avaliation.modalSendMail', [
            'AVALIATION' => $Avaliation,
        ]);

        if (1 == $request->input('json', 0)) {
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

    public function doModalSendMail(Request $request)
    {
        $Avaliation = mAvaliation::getModelByCodedId($request->input('cid', ''));
        if (!$Avaliation) {
            return $this->modelNotFoundResponse();
        }

        $email = $request->input('email', '');
        $link = $this->getCachedAvaliationLink($Avaliation, 'mail', $email, self::SEND_MAIL_HOURS);

        $ret = $Avaliation->sendLinkByMail($email, $link);
        if ($ret->isError()) {
            return $this->returnResponse(true, $ret->getMessage(), [], Response::HTTP_OK);
        }

        return $this->returnResponse(
            false,
            __('messages.pages.avaliation.modalSendWhats.successMessage'),
            [],
            Response::HTTP_OK
        );
    }

    /** signed route */
    public function showMyAvaliation(string $codedId)
    {
        return $this->viewReportPDF(request(), $codedId);
    }

    private function formatSaveRequest(Request $request): array
    {
        $form = [];
        $form['cid'] = $request->input('f-cid') ?? null;
        $form['acid'] = $request->input('f-acid') ?? null;
        $form['date'] = $request->input('f-date') ?? null;
        $form['calculate_perc_fat_by'] = $request->input('f-cfpb') ?? null;
        $form['weight_kg'] = $request->input('f-weight') ?? null;
        $form['body_fat_perc'] = $request->input('f-bfat') ?? null;
        $form['skeletal_muscle_perc'] = $request->input('f-skeletal_mp') ?? null;
        $form['muscle_mass_perc'] = $request->input('f-muscle_mp') ?? null;
        $form['visceral_fat_level'] = $request->input('f-visceral_fat') ?? null;
        $form['basal_metabolism'] = $request->input('f-basal') ?? null;
        $form['body_age'] = $request->input('f-bage') ?? null;
        $form['body_water_perc'] = $request->input('f-bwater') ?? null;
        $form['bone_mass_kg'] = $request->input('f-bmass') ?? null;

        $form['left_arm_lean_mass_kg'] = $request->input('f-la-lmass-kg') ?? null;
        $form['left_arm_lean_mass_perc'] = $request->input('f-la-lmass-perc') ?? null;
        $form['left_arm_fat_kg'] = $request->input('f-la-fat-kg') ?? null;
        $form['left_arm_fat_perc'] = $request->input('f-la-fat-perc') ?? null;
        $form['right_arm_lean_mass_kg'] = $request->input('f-ra-lmass-kg') ?? null;
        $form['right_arm_lean_mass_perc'] = $request->input('f-ra-lmass-perc') ?? null;
        $form['right_arm_fat_kg'] = $request->input('f-ra-fat-kg') ?? null;
        $form['right_arm_fat_perc'] = $request->input('f-ra-fat-perc') ?? null;
        $form['trunk_lean_mass_kg'] = $request->input('f-tr-lmass-kg') ?? null;
        $form['trunk_lean_mass_perc'] = $request->input('f-tr-lmass-perc') ?? null;
        $form['trunk_fat_kg'] = $request->input('f-tr-fat-kg') ?? null;
        $form['trunk_fat_perc'] = $request->input('f-tr-fat-perc') ?? null;
        $form['left_leg_lean_mass_kg'] = $request->input('f-ll-lmass-kg') ?? null;
        $form['left_leg_lean_mass_perc'] = $request->input('f-ll-lmass-perc') ?? null;
        $form['left_leg_fat_kg'] = $request->input('f-ll-fat-kg') ?? null;
        $form['left_leg_fat_perc'] = $request->input('f-ll-fat-perc') ?? null;
        $form['right_leg_lean_mass_kg'] = $request->input('f-rl-lmass-kg') ?? null;
        $form['right_leg_lean_mass_perc'] = $request->input('f-rl-lmass-perc') ?? null;
        $form['right_leg_fat_kg'] = $request->input('f-rl-fat-kg') ?? null;
        $form['right_leg_fat_perc'] = $request->input('f-rl-fat-perc') ?? null;

        $form['chest_circ_cm'] = $request->input('f-chest_circ') ?? null;
        $form['right_arm_circ_cm'] = $request->input('f-rarm_circ') ?? null;
        $form['left_arm_circ_cm'] = $request->input('f-larm_circ') ?? null;
        $form['waist_circ_cm'] = $request->input('f-waist_circ') ?? null;
        $form['right_forearm_circ_cm'] = $request->input('f-rfarm_circ') ?? null;
        $form['left_forearm_circ_cm'] = $request->input('f-lfarm_circ') ?? null;
        $form['abdomen_circ_cm'] = $request->input('f-abd_circ') ?? null;
        $form['right_thigh_circ_cm'] = $request->input('f-rthi_circ') ?? null;
        $form['left_thigh_circ_cm'] = $request->input('f-lthi_circ') ?? null;
        $form['hip_circ_cm'] = $request->input('f-hip_circ') ?? null;
        $form['right_calf_circ_cm'] = $request->input('f-rcalf_circ') ?? null;
        $form['left_calf_circ_cm'] = $request->input('f-lcalf_circ') ?? null;
        $form['neck_circ_cm'] = $request->input('f-neck_circ') ?? null;

        $form['skin_folds_formula'] = $request->input('f-sf-form') ?? null;
        $form['skin_folds_chest_cm'] = $request->input('f-skin_folds_chest') ?? null;
        $form['skin_folds_abdominal_cm'] = $request->input('f-skin_folds_abdominal') ?? null;
        $form['skin_folds_thigh_cm'] = $request->input('f-skin_folds_thigh') ?? null;
        $form['skin_folds_tricep_cm'] = $request->input('f-skin_folds_tricep') ?? null;
        $form['skin_folds_axilla_cm'] = $request->input('f-skin_folds_axilla') ?? null;
        $form['skin_folds_subscapular_cm'] = $request->input('f-skin_folds_subscapular') ?? null;
        $form['skin_folds_suprailiac_cm'] = $request->input('f-skin_folds_suprailiac') ?? null;
        $form['skin_folds_bicep_cm'] = $request->input('f-skin_folds_bicep') ?? null;

        $form['client_notes'] = $request->input('f-cnotes') ?? null;
        $form['private_notes'] = $request->input('f-pnotes') ?? null;

        $form['revaluation_date'] = $request->input('f-rev-date') ?? null;
        $RevDateFeature = new RevaluationDate();
        if (!$RevDateFeature->validate()) {
            $form['revaluation_date'] = null;
        }

        // get Client
        $Client = Client::getModelByCodedId($form['cid']);
        $form['client_id'] = $Client?->id;

        // format deadline from d/m/Y to Y-m-d
        if (null !== $form['date']) {
            $form['date'] = SysUtils::reformatDate($form['date'], __('messages.dateFormat'), 'Y-m-d');
        }
        if (null !== $form['revaluation_date']) {
            $form['revaluation_date'] = SysUtils::reformatDate($form['revaluation_date'], __('messages.dateFormat'), 'Y-m-d');
        }

        // format number to db
        foreach ([
            'weight_kg',
            'body_fat_perc',
            'skeletal_muscle_perc',
            'muscle_mass_perc',
            'visceral_fat_level',
            'body_water_perc',
            'bone_mass_kg',
            'right_arm_lean_mass_kg',
            'right_arm_lean_mass_perc',
            'right_arm_fat_kg',
            'right_arm_fat_perc',
            'left_arm_lean_mass_kg',
            'left_arm_lean_mass_perc',
            'left_arm_fat_kg',
            'left_arm_fat_perc',
            'trunk_lean_mass_kg',
            'trunk_lean_mass_perc',
            'trunk_fat_kg',
            'trunk_fat_perc',
            'right_leg_lean_mass_kg',
            'right_leg_lean_mass_perc',
            'right_leg_fat_kg',
            'right_leg_fat_perc',
            'left_leg_lean_mass_kg',
            'left_leg_lean_mass_perc',
            'left_leg_fat_kg',
            'left_leg_fat_perc',
            'chest_circ_cm',
            'right_arm_circ_cm',
            'left_arm_circ_cm',
            'waist_circ_cm',
            'right_forearm_circ_cm',
            'left_forearm_circ_cm',
            'abdomen_circ_cm',
            'right_thigh_circ_cm',
            'left_thigh_circ_cm',
            'hip_circ_cm',
            'right_calf_circ_cm',
            'left_calf_circ_cm',
            'neck_circ_cm',
            'skin_folds_chest_cm',
            'skin_folds_abdominal_cm',
            'skin_folds_thigh_cm',
            'skin_folds_tricep_cm',
            'skin_folds_suprailiac_cm',
            'skin_folds_axilla_cm',
            'skin_folds_subscapular_cm',
            'skin_folds_bicep_cm',
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

    private function saveAvaliationsPhotos(mAvaliation $Avaliation, Request $request): void
    {
        $arrLoop = [
            [
                'name' => 'f-photo_front_url',
                'fieldName' => 'photo_front_url',
                'method' => 'setPhotoFrontUrl',
                'removeMethod' => 'removePhotoFrontUrl',
            ],
            [
                'name' => 'f-photo_right_url',
                'fieldName' => 'photo_right_url',
                'method' => 'setPhotoRightUrl',
                'removeMethod' => 'removePhotoRightUrl',
            ],
            [
                'name' => 'f-photo_left_url',
                'fieldName' => 'photo_left_url',
                'method' => 'setPhotoLeftUrl',
                'removeMethod' => 'removePhotoLeftUrl',
            ],
            [
                'name' => 'f-photo_rear_url',
                'fieldName' => 'photo_rear_url',
                'method' => 'setPhotoRearUrl',
                'removeMethod' => 'removePhotoRearUrl',
            ],
        ];

        foreach ($arrLoop as $loop) {
            // check if we need to remove image
            $inputRemove = 'remove_' . $loop['name'];
            if ($request->input($inputRemove) == 1) {
                $Avaliation->{$loop['removeMethod']}($loop['fieldName']);
                continue;
            }

            // save image
            $file = $request->file($loop['name']);
            $Avaliation->{$loop['method']}($file);
        }
    }

    private function getCachedAvaliationLink(mAvaliation $Avaliation, string $channel, string $key, int $ttlHours): string
    {
        $cacheKey = 'avalation_send_' . $channel . '_' . $Avaliation->id . '_' . $key;

        if (cache()->has($cacheKey)) {
            $cachedLink = cache()->get($cacheKey);
            if (is_string($cachedLink) && $cachedLink !== '') {
                return $cachedLink;
            }
        }

        $portalLink = URL::temporarySignedRoute(
            'app.avaliation.showMyAvaliation',
            now()->addHours($ttlHours),
            ['codedId' => $Avaliation->codedId]
        );
        $shortUrl = \App\Models\UrlShort::make($portalLink);

        cache()->put($cacheKey, $shortUrl, $ttlHours * 60 * 60);

        return $shortUrl;
    }

    private function modelNotFoundResponse(): \Illuminate\Http\JsonResponse
    {
        return $this->returnResponse(
            true,
            __('messages.saveModelNotFound', [
                'modelName' => __('messages.models.Avaliation.name')
            ]),
            [],
            Response::HTTP_OK
        );
    }
}
