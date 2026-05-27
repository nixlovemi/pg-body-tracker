<?php

namespace App\Console\Commands;

use App\Services\Checkin\CheckinDispatchService;
use Illuminate\Console\Command;

class DispatchDueCheckins extends Command
{
    public const COMMAND_NAME = 'checkin:dispatch-due';
    public const OPTION_USER_ID = 'user_id';
    public const OPTION_DRY_RUN = 'dry-run';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'checkin:dispatch-due {--user_id= : Nutritionist (manager) user id filter} {--dry-run : Simulate dispatch without sending emails or persisting schedule changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch due check-in links to clients for active premium nutritionists';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(CheckinDispatchService $service)
    {
        $userIdOption = $this->option(self::OPTION_USER_ID);
        $nutritionistUserId = null;

        if ($userIdOption !== null && $userIdOption !== '') {
            if (!ctype_digit((string) $userIdOption) || (int) $userIdOption <= 0) {
                $this->error('Invalid value for --user_id. Use a positive nutritionist user id (manager).');
                return self::FAILURE;
            }

            $nutritionistUserId = (int) $userIdOption;
        }

        $isDryRun = (bool) $this->option(self::OPTION_DRY_RUN);
        $result = $service->dispatchDue($nutritionistUserId, $isDryRun);

        if ($isDryRun) {
            $this->warn('Dry run mode enabled: no emails were sent and no check-in schedule state was persisted.');
        }

        $this->info('Check-in dispatch finished.');
        if ($nutritionistUserId) {
            $this->line('Nutritionist user id scope: ' . $nutritionistUserId);
        }
        $this->line('Configs scanned: ' . $result['configs_scanned']);
        $this->line('Eligible configs: ' . $result['eligible']);
        if ($isDryRun) {
            $this->line('Links that would be dispatched: ' . $result['would_dispatch']);
        } else {
            $this->line('Links dispatched: ' . $result['links_dispatched']);
        }
        $this->line('Configs skipped: ' . $result['skipped']);
        $this->line('Errors: ' . $result['errors']);

        return self::SUCCESS;
    }
}
