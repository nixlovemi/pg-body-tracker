<?php

namespace Tests\Unit\Helpers\Avaliation;

use App\Helpers\Avaliation\WaistAbdomenCircumference;
use App\Models\Avaliation;
use App\Models\Client;
use Mockery;
use Tests\TestCase;

class WaistAbdomenCircumferenceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testWomanRiskRankingUsesWaistCutoffs(): void
    {
        $avaliation = $this->makeAvaliationMock(waist: 79.0, abdomen: 84.0, isMale: false);
        $helper = new WaistAbdomenCircumference($avaliation);

        $this->assertSame(1, $helper->getFieldInfo()['rank']);

        $avaliationModerate = $this->makeAvaliationMock(waist: 82.0, abdomen: 88.0, isMale: false);
        $helperModerate = new WaistAbdomenCircumference($avaliationModerate);

        $this->assertSame(2, $helperModerate->getFieldInfo()['rank']);

        $avaliationHigh = $this->makeAvaliationMock(waist: 90.0, abdomen: 95.0, isMale: false);
        $helperHigh = new WaistAbdomenCircumference($avaliationHigh);

        $this->assertSame(3, $helperHigh->getFieldInfo()['rank']);
    }

    public function testManRiskRankingUsesWaistCutoffs(): void
    {
        $avaliation = $this->makeAvaliationMock(waist: 93.0, abdomen: 99.0, isMale: true);
        $helper = new WaistAbdomenCircumference($avaliation);
        $this->assertSame(1, $helper->getFieldInfo()['rank']);

        $avaliationModerate = $this->makeAvaliationMock(waist: 98.0, abdomen: 100.0, isMale: true);
        $helperModerate = new WaistAbdomenCircumference($avaliationModerate);
        $this->assertSame(2, $helperModerate->getFieldInfo()['rank']);

        $avaliationHigh = $this->makeAvaliationMock(waist: 104.0, abdomen: 108.0, isMale: true);
        $helperHigh = new WaistAbdomenCircumference($avaliationHigh);
        $this->assertSame(3, $helperHigh->getFieldInfo()['rank']);
    }

    public function testFieldLabelContainsBothCircumferences(): void
    {
        $avaliation = $this->makeAvaliationMock(waist: 82.4, abdomen: 89.1, isMale: false);
        $helper = new WaistAbdomenCircumference($avaliation);

        $label = $helper->getFieldInfo()['fieldLabel'];

        $this->assertStringContainsString((string) __('messages.models.Avaliation.fields.waist_circ_cm'), $label);
        $this->assertStringContainsString((string) __('messages.models.Avaliation.fields.abdomen_circ_cm'), $label);
        $this->assertStringContainsString('82', $label);
        $this->assertStringContainsString('89', $label);
    }

    private function makeAvaliationMock(float $waist, float $abdomen, bool $isMale): Avaliation
    {
        $client = Mockery::mock(Client::class);
        $client->shouldReceive('isMale')->andReturn($isMale);

        /** @var Avaliation&\Mockery\LegacyMockInterface $avaliation */
        $avaliation = Mockery::mock(Avaliation::class)->makePartial();
        $avaliation->client = $client;
        $avaliation->waist_circ_cm = $waist;
        $avaliation->abdomen_circ_cm = $abdomen;

        return $avaliation;
    }
}
