<?php

namespace App\Console\Commands;

use App\Services\AvaliationPdfCacheService;
use Illuminate\Console\Command;

class CleanupExpiredPdfCaches extends Command
{
    public const COMMAND_NAME = 'pdf:cleanup-expired';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = self::COMMAND_NAME
        . ' {--days=30 : Delete PDFs older than this many days}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired PDF caches (default: older than 30 days).';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');

        if ($days <= 0) {
            $this->error('Days must be greater than 0.');
            return 1;
        }

        $service = app(AvaliationPdfCacheService::class);
        $deleted = $service->cleanupExpiredCaches($days);

        if ($deleted > 0) {
            $this->info("Deleted {$deleted} expired PDF cache(s) older than {$days} day(s).");
        } else {
            $this->info("No expired PDF caches found (older than {$days} day(s)).");
        }

        return 0;
    }
}
