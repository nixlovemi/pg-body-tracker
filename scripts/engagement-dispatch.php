<?php

declare(strict_types=1);

use App\Console\Commands\DispatchEngagementDigest;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Artisan;

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    echo "This script must be executed via CLI.\n";
    exit(1);
}

$projectRoot = dirname(__DIR__);

require $projectRoot . '/vendor/autoload.php';

$app = require $projectRoot . '/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$args = getopt('', [DispatchEngagementDigest::OPTION_USER_ID . '::', 'user-id::', DispatchEngagementDigest::OPTION_DRY_RUN, 'help']);

if (isset($args['help'])) {
    echo "Usage:\n";
    echo "  php scripts/engagement-dispatch.php [--user_id=1|--user-id=1] [--dry-run]\n\n";
    echo "Examples:\n";
    echo "  php scripts/engagement-dispatch.php --user_id=1\n";
    echo "  php scripts/engagement-dispatch.php --user-id=1\n";
    echo "  php scripts/engagement-dispatch.php --dry-run\n";
    exit(0);
}

$options = [];
$userId = null;
if (isset($args[DispatchEngagementDigest::OPTION_USER_ID]) && $args[DispatchEngagementDigest::OPTION_USER_ID] !== false && $args[DispatchEngagementDigest::OPTION_USER_ID] !== '') {
    if (!ctype_digit((string) $args[DispatchEngagementDigest::OPTION_USER_ID]) || (int) $args[DispatchEngagementDigest::OPTION_USER_ID] <= 0) {
        fwrite(STDERR, "Invalid value for --user_id. Use a positive integer.\n");
        exit(1);
    }
    $userId = (int) $args[DispatchEngagementDigest::OPTION_USER_ID];
} elseif (isset($args['user-id']) && $args['user-id'] !== false && $args['user-id'] !== '') {
    if (!ctype_digit((string) $args['user-id']) || (int) $args['user-id'] <= 0) {
        fwrite(STDERR, "Invalid value for --user-id. Use a positive integer.\n");
        exit(1);
    }
    $userId = (int) $args['user-id'];
}

if ($userId !== null) {
    $options['--' . DispatchEngagementDigest::OPTION_USER_ID] = $userId;
}
if (isset($args[DispatchEngagementDigest::OPTION_DRY_RUN])) {
    $options['--' . DispatchEngagementDigest::OPTION_DRY_RUN] = true;
}

$exitCode = Artisan::call(DispatchEngagementDigest::COMMAND_NAME, $options);

$response = [
    'message' => 'Engagement dispatch finished.',
    'output' => trim(Artisan::output()),
    'exit_code' => $exitCode,
];

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . PHP_EOL;

exit($exitCode);
