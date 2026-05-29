<?php

declare(strict_types=1);

use App\Console\Commands\SnapshotPatientInsights;
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

$args = getopt('', [SnapshotPatientInsights::OPTION_DRY_RUN, 'help']);

if (isset($args['help'])) {
    echo "Usage:\n";
    echo "  php scripts/patient-insights-snapshot.php [--dry-run]\n\n";
    echo "Examples:\n";
    echo "  php scripts/patient-insights-snapshot.php\n";
    echo "  php scripts/patient-insights-snapshot.php --dry-run\n";
    exit(0);
}

$options = [];
if (isset($args[SnapshotPatientInsights::OPTION_DRY_RUN])) {
    $options['--' . SnapshotPatientInsights::OPTION_DRY_RUN] = true;
}

$exitCode = Artisan::call(SnapshotPatientInsights::COMMAND_NAME, $options);

$response = [
    'message' => 'Patient insights snapshot finished.',
    'output' => trim(Artisan::output()),
    'exit_code' => $exitCode,
];

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . PHP_EOL;

exit($exitCode);
