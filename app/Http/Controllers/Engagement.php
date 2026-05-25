<?php

namespace App\Http\Controllers;

use App\Console\Commands\DispatchEngagementDigest;
use App\Models\User;
use App\Models\UserEngagement;
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
            $options['--' . DispatchEngagementDigest::OPTION_USER_ID] = (int) $request->input('user_id');
        }

        Artisan::call(DispatchEngagementDigest::COMMAND_NAME, $options);

        return response()->json([
            'message' => 'Engagement dispatch finished.',
            'output' => trim(Artisan::output()),
        ]);
    }

    public function unsubscribe(string $codedId)
    {
        $user = User::getModelByCodedId($codedId);
        if (!$user) {
            abort(404);
        }

        UserEngagement::updateOrCreate(
            ['user_id' => $user->id],
            ['opt_out' => true]
        );

        return response()->view('app.engagement-unsubscribed', [
            'PAGE_TITLE' => __('messages.pages.engagement.unsubscribe.title'),
        ]);
    }
}
