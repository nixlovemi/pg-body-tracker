<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ConsumePdfQueue extends Command
{
    public const COMMAND_NAME = 'pdf:consume-queue';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = self::COMMAND_NAME
        . ' {--queue=pdf : Queue name to consume}'
        . ' {--connection=database : Queue connection}'
        . ' {--sleep=1 : Sleep seconds when waiting for jobs}'
        . ' {--tries=1 : Max attempts per job}'
        . ' {--timeout=300 : Job timeout in seconds}'
        . ' {--drain : Process until queue is empty (stop-when-empty)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Consume PDF generation queue jobs (local-friendly wrapper for queue:work).';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $queue = (string) $this->option('queue');
        $connection = (string) $this->option('connection');
        $sleep = (int) $this->option('sleep');
        $tries = (int) $this->option('tries');
        $timeout = (int) $this->option('timeout');
        $drain = (bool) $this->option('drain');

        if ($sleep < 0 || $tries <= 0 || $timeout <= 0) {
            $this->error('Invalid numeric options. Use sleep>=0, tries>0 and timeout>0.');
            return 1;
        }

        $options = [
            'connection' => $connection,
            '--queue' => $queue,
            '--sleep' => $sleep,
            '--tries' => $tries,
            '--timeout' => $timeout,
            '--force' => true,
        ];

        if ($drain) {
            $options['--stop-when-empty'] = true;
            $this->info('Consuming PDF queue until empty...');
        } else {
            $options['--once'] = true;
            $this->info('Consuming one PDF queue job...');
        }

        $exitCode = Artisan::call('queue:work', $options);
        $output = trim(Artisan::output());
        if ($output !== '') {
            $this->line($output);
        }

        return $exitCode;
    }
}
