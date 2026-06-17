<?php

declare(strict_types=1);

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Artisan;
use App\Console\Commands\ConsumePdfQueue;

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

$args = getopt('', ['queue::', 'connection::', 'sleep::', 'tries::', 'timeout::', 'drain', 'help']);

if (isset($args['help'])) {
    echo "Usage:\n";
    echo "  php scripts/pdf-cache-dispatch.php [--queue=pdf] [--connection=database] [--sleep=1] [--tries=1] [--timeout=300] [--drain]\n\n";
    echo "Defaults:\n";
    echo "  --queue=pdf\n";
    echo "  --connection=database\n";
    echo "  --sleep=1\n";
    echo "  --tries=1\n";
    echo "  --timeout=300\n";
    echo "  (without --drain: processes exactly one job and exits)\n\n";
    echo "Examples:\n";
    echo "  php scripts/pdf-cache-dispatch.php\n";
    echo "  php scripts/pdf-cache-dispatch.php --drain\n";
    exit(0);
}

$queue = (string) ($args['queue'] ?? 'pdf');
$connection = (string) ($args['connection'] ?? 'database');
$sleep = (int) ($args['sleep'] ?? 1);
$tries = (int) ($args['tries'] ?? 1);
$timeout = (int) ($args['timeout'] ?? 300);
$drain = isset($args['drain']);

if ($sleep < 0 || $tries <= 0 || $timeout <= 0) {
    fwrite(STDERR, "Invalid numeric options.\n");
    exit(1);
}

$options = [
    '--connection' => $connection,
    '--queue' => $queue,
    '--sleep' => $sleep,
    '--tries' => $tries,
    '--timeout' => $timeout,
];

if ($drain) {
    $options['--drain'] = true;
}

$exitCode = Artisan::call(ConsumePdfQueue::COMMAND_NAME, $options);

$response = [
    'message' => 'PDF queue dispatch finished.',
    'output' => trim(Artisan::output()),
    'exit_code' => $exitCode,
    'options' => [
        'connection' => $connection,
        'queue' => $queue,
        'drain' => $drain,
    ],
];

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . PHP_EOL;

exit($exitCode);
