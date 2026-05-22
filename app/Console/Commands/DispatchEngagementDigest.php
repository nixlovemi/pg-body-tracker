<?php

namespace App\Console\Commands;

use App\Services\Engagement\EngagementDigestService;
use Illuminate\Console\Command;

/**
 * Class DispatchEngagementDigest
 *
 * This command dispatches engagement digest emails for users.
 * Usage: php /home/pgbody/domains/pgbodytracker.com.br/public_html/artisan engagement:dispatch
 * Usage local: php artisan engagement:dispatch --user_id=123 --dry-run
 */
class DispatchEngagementDigest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'engagement:dispatch {--user_id=} {--dry-run : Simulate dispatch without queueing jobs or writing state}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch engagement digest emails for due users';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(EngagementDigestService $service)
    {
        $forcedUserId = $this->option('user_id') ? (int) $this->option('user_id') : null;
        $isDryRun = (bool) $this->option('dry-run');
        $result = $service->dispatchDueUsers($forcedUserId, $isDryRun);

        if ($isDryRun) {
            $this->warn('Dry run mode enabled: no emails were queued and no engagement state was persisted.');
        }

        $this->info('Engagement dispatch finished.');
        $this->line('Users scanned: ' . $result['users_scanned']);
        if ($isDryRun) {
            $this->line('Emails that would be dispatched: ' . $result['would_dispatch']);
        } else {
            $this->line('Emails dispatched: ' . $result['emails_dispatched']);
        }
        $this->line('Users skipped: ' . $result['users_skipped']);

        return self::SUCCESS;
    }
}
