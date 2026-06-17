<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Cache;
use QuickChart;

class ChartPhp extends Component
{
    public string $base64Img = '';
    private CONST CACHED_KEY_JSON = 'json';
    private CONST CACHED_KEY_BASE64_IMG = 'base64Img';

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        public string $elementId,
        public string $config, // JSON string
        public int $width = 500,
        public int $height = 300,
    ) {
        $this->initBase64Img();
    }

    private function initBase64Img(): void
    {
        // check for cache
        $imageEncodedBinary = $this->getCachedImg();
        if ($imageEncodedBinary !== '') {
            $this->base64Img = $imageEncodedBinary;
            return;
        }

        $chart = new QuickChart(array(
            'width' => $this->width,
            'height' => $this->height,
        ));

        $chart->setConfig($this->config);
        $chart->setVersion('2.9.4'); // template/start-bootstrap/vendor/chart.js/Chart.min.js
        $this->base64Img = base64_encode($chart->toBinary());
        $this->cacheImg();
    }

    private function getCachedImg(): string
    {
        $cacheKey = $this->getCacheKey();
        if (false === Cache::has($cacheKey)) {
            return '';
        }

        // get the cached item
        $cachedItem = Cache::get($cacheKey);
        $cachedJson = $cachedItem[self::CACHED_KEY_JSON] ?? '';
        if ($cachedJson !== $this->config) {
            return '';
        }

        return $cachedItem[self::CACHED_KEY_BASE64_IMG] ?? '';
    }

    private function cacheImg(): void
    {
        $cacheKey = $this->getCacheKey();
        $cachedItem = [
            self::CACHED_KEY_JSON => $this->config,
            self::CACHED_KEY_BASE64_IMG => $this->base64Img,
        ];
        $oneDayInSeconds = 86400;
        Cache::put($cacheKey, $cachedItem, $oneDayInSeconds);
    }

    private function getCacheKey(): string
    {
        // OPTIMIZATION: Use hash of config instead of elementId to allow cache reuse
        // across different avaliations that have identical chart configurations.
        // This significantly improves cache hit rate in PDF generation.
        return 'chart-' . hash('sha256', $this->config);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.chart-php');
    }
}
