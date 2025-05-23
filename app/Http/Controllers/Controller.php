<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Return JSON response - API
     */
    protected function returnResponse (
        bool $error,
        string $message,
        array $data,
        ?int $responseCode
    ) {
        $ret = new ApiResponse(
            $error,
            ($error && empty($message)) ? 'Erro na requisição': $message,
            $data
        );

        if (is_null($responseCode)) {
            $responseCode = ($error) ? Response::HTTP_INTERNAL_SERVER_ERROR: Response::HTTP_OK;
        }

        return response()->json($ret->getArrayResponse(), $responseCode);
    }

    protected function getValidateMessage(ApiResponse $validate): string
    {
        return ApiResponse::getValidateMessage($validate);
    }

    protected function redirectWithError(string $routeName, string $errorMsg, array $routeParams=[]): \Illuminate\Http\RedirectResponse
    {
        return redirect()
            ->route($routeName, $routeParams)
            ->withInput()
            ->withErrors(['msg' => $errorMsg]);
    }

    protected function redirectSuccess(string $routeName, string $message, array $routeParams=[]): \Illuminate\Http\RedirectResponse
    {
        return redirect()
            ->route($routeName, $routeParams)
            ->withSuccess($message);
    }
}
