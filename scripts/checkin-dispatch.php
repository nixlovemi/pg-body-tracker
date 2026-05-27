<?php

declare(strict_types=1);

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Artisan;

const COMMAND_NAME = 'checkin:dispatch-due';
const OPTION_USER_ID = 'user_id';
const OPTION_DRY_RUN = 'dry-run';

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

$args = getopt('', [OPTION_USER_ID . '::', 'user-id::', OPTION_DRY_RUN, 'help']);

if (isset($args['help'])) {
    echo "Usage:\n";
    echo "  php scripts/checkin-dispatch.php [--user_id=10|--user-id=10] [--dry-run]\n\n";
    echo "Notes:\n";
    echo "  --user_id must be the nutritionist (manager) user id, not a client id.\n\n";
    echo "Examples:\n";
    echo "  php scripts/checkin-dispatch.php --user_id=10\n";
    echo "  php scripts/checkin-dispatch.php --user-id=10\n";
    echo "  php scripts/checkin-dispatch.php --dry-run\n";
    exit(0);
}

$options = [];
$nutritionistUserId = null;
if (isset($args[OPTION_USER_ID]) && $args[OPTION_USER_ID] !== false && $args[OPTION_USER_ID] !== '') {
    if (!ctype_digit((string) $args[OPTION_USER_ID]) || (int) $args[OPTION_USER_ID] <= 0) {
        fwrite(STDERR, "Invalid value for --user_id. Use a positive nutritionist user id (manager).\n");
        exit(1);
    }
    $nutritionistUserId = (int) $args[OPTION_USER_ID];
} elseif (isset($args['user-id']) && $args['user-id'] !== false && $args['user-id'] !== '') {
    if (!ctype_digit((string) $args['user-id']) || (int) $args['user-id'] <= 0) {
        fwrite(STDERR, "Invalid value for --user-id. Use a positive nutritionist user id (manager).\n");
        exit(1);
    }
    $nutritionistUserId = (int) $args['user-id'];
}

if ($nutritionistUserId !== null) {
    $options['--' . OPTION_USER_ID] = $nutritionistUserId;
}
if (isset($args[OPTION_DRY_RUN])) {
    $options['--' . OPTION_DRY_RUN] = true;
}

$exitCode = Artisan::call(COMMAND_NAME, $options);

$response = [
    'message' => 'Check-in dispatch finished.',
    'output' => trim(Artisan::output()),
    'exit_code' => $exitCode,
];

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . PHP_EOL;

exit($exitCode);
