<?php

namespace Tests\Unit\Presenters;

use App\Presenters\SubscriptionUpgradePresenter;
use Tests\TestCase;

class SubscriptionUpgradePresenterTest extends TestCase
{
    public function testGetFeaturesFreePremiumLoadsNewFeatureLinesFromTranslationsAtRuntime(): void
    {
        $baseFree = SubscriptionUpgradePresenter::getFeaturesFreePremium(true);
        $basePremium = SubscriptionUpgradePresenter::getFeaturesFreePremium(false);

        app('translator')->addLines([
            'messages.pages.premium.freeVsPremium.features.line99Free' => 'Temp Feature Free 99',
            'messages.pages.premium.freeVsPremium.features.line99Premium' => 'Temp Feature Premium 99',
            'messages.pages.premium.freeVsPremium.features.line99FreeEnabled' => false,
        ], app()->getLocale());

        $updatedFree = SubscriptionUpgradePresenter::getFeaturesFreePremium(true);
        $updatedPremium = SubscriptionUpgradePresenter::getFeaturesFreePremium(false);

        $this->assertCount(count($baseFree) + 1, $updatedFree);
        $this->assertCount(count($basePremium) + 1, $updatedPremium);

        $freeFeature = collect($updatedFree)->first(function (array $item): bool {
            return ($item['label'] ?? '') === 'Temp Feature Free 99';
        });

        $premiumFeature = collect($updatedPremium)->first(function (array $item): bool {
            return ($item['label'] ?? '') === 'Temp Feature Premium 99';
        });

        $this->assertNotNull($freeFeature);
        $this->assertNotNull($premiumFeature);
        $this->assertSame(false, (bool) $freeFeature['free']);
    }
}
