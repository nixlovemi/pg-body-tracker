<?php

namespace App\Jobs;

use App\Models\Avaliation;
use App\Services\AvaliationPdfCacheService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateAvaliationPdfCacheJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 180;

    public function __construct(
        public int $avaliationId,
        public bool $includeGraphs = true,
        public bool $includePictures = true,
    ) {
    }

    public function uniqueId(): string
    {
        return sprintf(
            'avaliation-pdf:%d:%d:%d',
            $this->avaliationId,
            $this->includeGraphs ? 1 : 0,
            $this->includePictures ? 1 : 0
        );
    }

    /**
     * Execute the job.
     */
    public function handle(AvaliationPdfCacheService $pdfCacheService): void
    {
        $avaliation = Avaliation::find($this->avaliationId);
        if (!$avaliation) {
            return;
        }

        $pdfCacheService->ensureCurrentPdfCached(
            $avaliation,
            $this->includeGraphs,
            $this->includePictures
        );
    }
}
