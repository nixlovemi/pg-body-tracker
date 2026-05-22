<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class Engagement extends Controller
{
    public function run(Request $request): JsonResponse
    {
        $configuredToken = (string) env('ENGAGEMENT_ENDPOINT_TOKEN', '');
        $requestToken = (string) $request->header('X-Engagement-Key', '');

        if ($configuredToken === '' || $requestToken === '' || !hash_equals($configuredToken, $requestToken)) {
            abort(403);
        }

        $options = [];
        if ($request->filled('user_id')) {
            $options['--user_id'] = (int) $request->input('user_id');
        }

        Artisan::call('engagement:dispatch', $options);

        return response()->json([
            'message' => 'Engagement dispatch finished.',
            'output' => trim(Artisan::output()),
        ]);
    }
}
