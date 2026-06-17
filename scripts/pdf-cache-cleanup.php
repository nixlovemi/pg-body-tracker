<?php

declare(strict_types=1);

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Artisan;
use App\Console\Commands\CleanupExpiredPdfCaches;

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

$args = getopt('', ['days::', 'help']);

if (isset($args['help'])) {
    echo "Usage:\n";
    echo "  php scripts/pdf-cache-cleanup.php [--days=30]\n\n";
    echo "Options:\n";
    echo "  --days=N    Delete PDFs older than N days (default: 30)\n\n";
    echo "Examples:\n";
    echo "  php scripts/pdf-cache-cleanup.php\n";
    echo "  php scripts/pdf-cache-cleanup.php --days=60\n";
    exit(0);
}

$days = (int) ($args['days'] ?? 30);

if ($days <= 0) {
    fwrite(STDERR, "Invalid days option. Must be greater than 0.\n");
    exit(1);
}

$options = [
    '--days' => $days,
];

$exitCode = Artisan::call(CleanupExpiredPdfCaches::COMMAND_NAME, $options);

$response = [
    'message' => 'PDF cache cleanup finished.',
    'output' => trim(Artisan::output()),
    'exit_code' => $exitCode,
    'options' => [
        'days' => $days,
    ],
];

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . PHP_EOL;

exit($exitCode);
