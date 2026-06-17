---
applyTo: "app/Helpers/AvaliationGraph/*.php"
description: "Use when creating or editing AvaliationGraph helpers, Chart.js config arrays, graph table data, legend/scales structure, and Avaliation history plotting."
---

# Avaliation Graph Helpers

Use `App\Helpers\AvaliationGraph\AvaliationLeanMassGraphHelper::getConfig()` as the canonical reference for new graph helpers in this repository.

## Required `getConfig()` structure

Every graph helper must return this root shape:

```php
return [
    'type' => 'line',
    'data' => $data,
    'options' => [
        'responsive' => true,
        'title' => [
            'display' => false,
        ],
        'legend' => [
            'display' => false,
            'position' => 'top',
        ],
        'scales' => [
            'yAxes' => [[
                'ticks' => [
                    'suggestedMin' => $min - $stepSize,
                    'suggestedMax' => $max + $stepSize,
                    'stepSize' => $stepSize,
                    'padding' => 2.5,
                ],
                'scaleLabel' => [
                    'display' => true,
                    'labelString' => '...',
                ],
            ]],
            'xAxes' => [[
                'ticks' => ['padding' => 2.5],
                'scaleLabel' => ['display' => false],
            ]],
        ],
    ],
];
```

## Project conventions

- Put `legend` directly under `options`.
- Do not put `legend` inside `plugins` for these helpers.
- Use `scales.yAxes` and `scales.xAxes`.
- Do not switch these helpers to `scales.y` or `scales.x`.
- Prefer the same `title`, `legend`, `ticks`, and `scaleLabel` layout already used by existing helpers.
- Build the chart with the `date` field from `Avaliation`, not `created_at`.
- Keep table output in sync with chart points by calling `addBodyItem(...)` when a point is added.
- Use translations for labels with `__()`.
- Use domain constants for colors and sentinel values.

## Single-dataset helpers

Examples: trunk fat percentage and skeletal muscle percentage.

- Keep only the real metric dataset.
- Set `options.legend.display` to `false`.
- If no valid points exist, return `[]` so the UI can show the insufficient-data message.

Recommended dataset shape:

```php
$data['datasets'][] = [
    'label' => __('messages.components.avaliationReport.someMetric'),
    'data' => $points,
    'borderColor' => $lineColor,
    'backgroundColor' => 'transparent',
    'borderWidth' => 3,
    'fill' => false,
    'tension' => 0.1,
    'pointRadius' => 5,
    'pointBackgroundColor' => $lineColor,
];
```

## Multi-dataset helpers

Examples: waist vs abdomen, or charts that show value plus ideal band.

- Keep `legend.display` aligned with the number of visible datasets.
- If there are two or more visible metric lines, the legend can remain enabled.
- If the chart uses an ideal band, keep that band as background-only datasets and the visible metric lines as separate datasets.

## Helper layout checklist

For new files, keep this method layout unless there is a strong local reason not to:

1. `__construct()`
2. `getClassName()`
3. `getAvaliation()`
4. `getConfig()`
5. `appendCurrentChartPoint()` if needed
6. `appendChartPoint()`
7. `addTableHeaders()`
8. `initChartData()`

## Data handling checklist

- Collect previous evaluations with `getPreviousAvaliations($queryLimit)`.
- Append the current evaluation explicitly after the history loop.
- Skip entries that do not have usable values for the graph.
- Keep `arrValues` limited to values actually used to calculate min/max bounds.
- Format table values with `SysUtils::formatDbToNumber(...)`.
- Format labels with `SysUtils::reformatDate($av->date, 'Y-m-d', $dateFormat)`.

## Validation checklist

After changing or creating a graph helper:

1. Run `php -l` on the edited helper.
2. Run the narrowest relevant PHPUnit file for graph helpers.
3. Confirm the config shape matches an existing helper such as `AvaliationLeanMassGraphHelper`.
4. If the graph is single-dataset, confirm there is no clickable legend toggle.

## Anti-patterns to avoid

- Do not invent a new Chart.js config shape for just one helper.
- Do not mix `plugins.legend` with `options.legend` in this folder unless the whole graph family is being migrated intentionally.
- Do not use `created_at` for graph chronology in `Avaliation` graphs.
- Do not keep extra datasets that produce `undefined` or translation-key labels in the legend.
- Do not return partially-built configs when there are no valid points.
