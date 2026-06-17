<?php

namespace App\Services;

use App\Models\Avaliation;
use App\Presenters\AvaliationReportPresenter;
use App\View\Components\ChartPhp;
use Illuminate\Support\Facades\Log;

class AvaliationPdfWarmupService
{
    /**
     * Pre-warm heavy PDF assets (charts and photos) to speed up the first PDF open.
     */
    public function warmup(Avaliation $avaliation): void
    {
        try {
            $previousAvaliations = $avaliation->getPreviousAvaliationsForReports(9);

            foreach (AvaliationReportPresenter::getGraphData() as $graphItem) {
                $helperClass = $graphItem['helperClass'] ?? null;
                if (empty($helperClass) || !class_exists($helperClass)) {
                    continue;
                }

                $helper = new $helperClass($avaliation->id, true);
                if (method_exists($helper, 'setPreviousAvaliations')) {
                    $helper->setPreviousAvaliations($previousAvaliations);
                }

                $graphData = $helper->getData();
                $config = $graphData['config'] ?? '';
                $uid = $graphData['UID'] ?? '';
                if (empty($config) || empty($uid)) {
                    continue;
                }

                // Creating the component triggers chart generation and stores it in cache.
                new ChartPhp($uid, $config);
            }

            foreach (AvaliationReportPresenter::getImagesData() as $imageItem) {
                $fieldName = $imageItem['fieldName'] ?? '';
                $defaultImg = $imageItem['defaultImg'] ?? null;
                if (empty($fieldName)) {
                    continue;
                }

                // Triggers/caches base64 conversion for PDF image rendering.
                $avaliation->getPhotoBase64($fieldName, $defaultImg);
            }
        } catch (\Throwable $e) {
            // Warmup is best-effort and must never impact report rendering.
            Log::debug('PDF warmup failed', [
                'avaliationId' => $avaliation->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
