<?php

namespace App\Console\Commands;

use App\Services\PatientInsights\PatientInsightsSnapshotService;
use Illuminate\Console\Command;

class SnapshotPatientInsights extends Command
{
    public const COMMAND_NAME = 'patient-insights:snapshot';
    public const OPTION_DRY_RUN = 'dry-run';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'patient-insights:snapshot {--dry-run : Evaluate all clients without writing snapshots}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build free patient insights daily snapshots for all clients';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(PatientInsightsSnapshotService $service)
    {
        $isDryRun = (bool) $this->option(self::OPTION_DRY_RUN);
        $result = $service->snapshotDaily(null, !$isDryRun);

        if ($isDryRun) {
            $this->warn('Dry run mode enabled: snapshots were evaluated but not written.');
        }

        $this->info('Patient insights snapshot finished.');
        $this->line('Snapshot date: ' . $result['snapshot_date']);
        $this->line('Clients scanned: ' . $result['clients_scanned']);
        $this->line('Snapshots evaluated: ' . $result['snapshots_evaluated']);
        $this->line('Snapshots written: ' . $result['snapshots_written']);
        $this->line('Errors: ' . $result['errors']);

        return self::SUCCESS;
    }
}
