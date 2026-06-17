<?php

namespace App\Services;

use App\Models\Avaliation;
use App\Models\AvaliationPdfCache;
use App\Presenters\AvaliationReportPresenter;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AvaliationPdfCacheService
{
    private const STORAGE_DISK = 'local';
    private const STORAGE_FOLDER = 'avaliation-pdfs';
    private const STATUS_READY = 'ready';
    private const STATUS_PENDING = 'pending';
    private const MAX_VERSIONS_PER_AVALIATION = 3;

    /**
     * Ensure the current snapshot PDF exists and is stored locally.
     */
    public function ensureCurrentPdfCached(
        Avaliation $avaliation,
        bool $includeGraphs = true,
        bool $includePictures = true
    ): ?AvaliationPdfCache {
        $snapshotHash = $this->buildSnapshotHash($avaliation, $includeGraphs, $includePictures);

        $cache = AvaliationPdfCache::firstOrCreate(
            [
                'avaliation_id' => $avaliation->id,
                'snapshot_hash' => $snapshotHash,
            ],
            [
                'storage_path' => $this->buildStoragePath($avaliation->id, $snapshotHash),
                'status' => self::STATUS_PENDING,
            ]
        );

        if ($cache->status === self::STATUS_READY && $this->exists($cache->storage_path)) {
            return $cache;
        }

        try {
            $pdfBinary = $this->renderPdfBinary($avaliation, $includeGraphs, $includePictures);
            Storage::disk(self::STORAGE_DISK)->put($cache->storage_path, $pdfBinary);

            $cache->status = self::STATUS_READY;
            $cache->file_size = strlen($pdfBinary);
            $cache->generated_at = now();
            $cache->save();

            $this->cleanupOldVersions($avaliation->id);

            return $cache;
        } catch (\Throwable $e) {
            Log::debug('Failed to cache PDF', [
                'avaliationId' => $avaliation->id,
                'hash' => $snapshotHash,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    public function buildSnapshotHash(
        Avaliation $avaliation,
        bool $includeGraphs = true,
        bool $includePictures = true
    ): string {
        $avaliation->loadMissing(['client.user.info']);

        $payload = [
            'schemaVersion' => 1,
            'locale' => app()->getLocale(),
            'includeGraphs' => $includeGraphs,
            'includePictures' => $includePictures,
            'avaliation' => $this->normalizeArray($this->filterAvaliationAttributes($avaliation->getAttributes())),
            'clientUpdatedAt' => optional($avaliation->client?->updated_at)?->timestamp,
            'userInfoUpdatedAt' => optional($avaliation->client?->user?->info?->updated_at)?->timestamp,
        ];

        return hash('sha256', json_encode($payload));
    }

    public function getCurrentSnapshotCache(
        Avaliation $avaliation,
        bool $includeGraphs = true,
        bool $includePictures = true
    ): ?AvaliationPdfCache {
        $snapshotHash = $this->buildSnapshotHash($avaliation, $includeGraphs, $includePictures);

        return AvaliationPdfCache::where('avaliation_id', $avaliation->id)
            ->where('snapshot_hash', $snapshotHash)
            ->first();
    }

    public function ensurePendingCacheRecord(
        Avaliation $avaliation,
        bool $includeGraphs = true,
        bool $includePictures = true
    ): AvaliationPdfCache {
        $snapshotHash = $this->buildSnapshotHash($avaliation, $includeGraphs, $includePictures);

        return AvaliationPdfCache::firstOrCreate(
            [
                'avaliation_id' => $avaliation->id,
                'snapshot_hash' => $snapshotHash,
            ],
            [
                'storage_path' => $this->buildStoragePath($avaliation->id, $snapshotHash),
                'status' => self::STATUS_PENDING,
            ]
        );
    }

    public function isReadyCache(?AvaliationPdfCache $cache): bool
    {
        return $cache !== null
            && $cache->status === self::STATUS_READY
            && !empty($cache->storage_path)
            && $this->exists($cache->storage_path);
    }

    /**
     * Build absolute file path for response()->file.
     */
    public function absolutePath(string $relativePath): string
    {
        return storage_path('app' . DIRECTORY_SEPARATOR . $relativePath);
    }

    private function renderPdfBinary(
        Avaliation $avaliation,
        bool $includeGraphs,
        bool $includePictures
    ): string {
        $previousAvaliations = $avaliation->getPreviousAvaliationsForReports(9);
        $infoCardsData = AvaliationReportPresenter::getInfoCardsData($avaliation);

        $pdf = Pdf::loadView('app.avaliation.viewReportPDF', [
            'AVALIATION' => $avaliation,
            'PREVIOUS_AVALIATIONS' => $previousAvaliations,
            'INFO_CARDS_DATA' => $infoCardsData,
            'INCLUDE_GRAPHS' => $includeGraphs,
            'INCLUDE_PICTURES' => $includePictures,
        ]);

        return $pdf->output();
    }

    private function buildStoragePath(int $avaliationId, string $hash): string
    {
        return sprintf('%s/%d/%s.pdf', self::STORAGE_FOLDER, $avaliationId, $hash);
    }

    private function exists(string $relativePath): bool
    {
        return Storage::disk(self::STORAGE_DISK)->exists($relativePath);
    }

    private function filterAvaliationAttributes(array $attributes): array
    {
        unset(
            $attributes['id'],
            $attributes['codedId'],
            $attributes['created_at'],
            $attributes['updated_at']
        );

        return $attributes;
    }

    private function normalizeArray(array $input): array
    {
        ksort($input);
        foreach ($input as $key => $value) {
            if (is_array($value)) {
                $input[$key] = $this->normalizeArray($value);
                continue;
            }

            if ($value === null) {
                $input[$key] = null;
                continue;
            }

            if (is_bool($value)) {
                $input[$key] = (int) $value;
                continue;
            }

            if (is_numeric($value)) {
                $input[$key] = (string) $value;
            }
        }

        return $input;
    }

    private function cleanupOldVersions(int $avaliationId): void
    {
        $oldItems = AvaliationPdfCache::where('avaliation_id', $avaliationId)
            ->where('status', self::STATUS_READY)
            ->orderByDesc('generated_at')
            ->orderByDesc('id')
            ->skip(self::MAX_VERSIONS_PER_AVALIATION)
            ->get();

        foreach ($oldItems as $item) {
            if (!empty($item->storage_path)) {
                Storage::disk(self::STORAGE_DISK)->delete($item->storage_path);
            }
            $item->delete();
        }
    }

    /**
     * Clean up PDFs older than the specified number of days.
     * Returns count of deleted records.
     */
    public function cleanupExpiredCaches(int $daysOld = 30): int
    {
        $expiryDate = now()->subDays($daysOld);

        $expiredItems = AvaliationPdfCache::where('generated_at', '<', $expiryDate)->get();

        $count = 0;
        foreach ($expiredItems as $item) {
            if (!empty($item->storage_path)) {
                Storage::disk(self::STORAGE_DISK)->delete($item->storage_path);
            }
            $item->delete();
            $count++;
        }

        return $count;
    }
}
