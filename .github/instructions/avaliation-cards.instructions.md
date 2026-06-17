---
applyTo: "app/Helpers/Avaliation/*.php"
description: "Use when creating or editing Avaliation card helpers, field info arrays, card rankings, ideal ranges, and Avaliation report card integration."
---

# Avaliation Cards

Use `App\Helpers\Avaliation\Weight`, `App\Helpers\Avaliation\BodyMassIndex`, and `App\Helpers\Avaliation\TrunkFatPercentage` as references when creating new report cards.

## Card architecture

Each report card is normally made of four parts:

1. A helper in `app/Helpers/Avaliation/` extending `AvaliationFieldInfoAbstract`
2. A forwarding method in `app/Models/Avaliation.php`
3. A card entry in `App\Presenters\AvaliationReportPresenter::getInfoCardsData()`
4. Translation keys in `resources/lang/pt_BR/messages.php` and `resources/lang/en/messages.php`

## Required helper contract

New helpers should extend `AvaliationFieldInfoAbstract` and implement:

- `getFieldSuffix()`
- `getFieldValue()`
- `getFieldLabel()`
- `getManRanking()`
- `getWomanRanking()`
- `getRankingLabels()`
- `getRankingColors()`

Override `defineIdealValues()` when the default ranking-derived ideal range is not enough.

Override `getRankNbr()` when the diagnosis should be based on another metric instead of `getFieldValue()`.

## Recommended card behavior

- Use domain calculations already present in `Avaliation` whenever possible.
- Use `Constants::RETURN_INT_CANT_CALCULATE` when inputs are missing.
- Use `SysUtils::formatDbToNumber(...)` for numeric labels.
- Keep `fieldLabel` human-friendly, for example `62.7kg` or `21.8kg/m²`.
- Keep `idealLabel` as a compact reference range whenever that range is meaningful.
- Use translation keys instead of literal diagnosis strings.

## Ranking guidance

- Rankings should reflect the diagnosis shown in the card, not just an arbitrary color.
- If the visible result is an estimated target, it is acceptable for the diagnosis to compare the current patient value against the target range.
- Keep ranking colors consistent with existing cards and `Constants::RANK_COLOR_*`.

## Presenter integration

Add the card to `AvaliationReportPresenter::getInfoCardsData()` with:

- `method`: the forwarding method added to `Avaliation`
- `title`: translation key under `messages.components.avaliationReport`
- `showReference`: `true` when the card has an ideal range worth displaying

## Model integration

Expose helpers from `Avaliation` using the existing forwarding pattern:

```php
public function getSomeCardInfo(): array
{
    $SomeCard = new \App\Helpers\Avaliation\SomeCard($this);
    return $SomeCard->getFieldInfo();
}
```

## Validation checklist

After adding or changing a card:

1. Run `php -l` on the helper, presenter, and model if they changed.
2. Add or update a focused PHPUnit test for the helper.
3. Verify translations exist in both `pt_BR` and `en`.
4. Confirm `showReference` matches the behavior of the card.
5. Confirm the diagnosis makes sense for the displayed result.

## Anti-patterns to avoid

- Do not add a card helper without wiring it into `Avaliation` and the presenter.
- Do not use scattered literal strings for diagnosis labels.
- Do not expose a reference range when the helper cannot calculate meaningful bounds.
- Do not base the diagnosis on a different metric unless that is intentional and documented in the helper.
