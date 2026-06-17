<?php

namespace Tests\Unit\Models;

use App\Helpers\Constants;
use App\Models\Avaliation;
use Mockery;
use Tests\TestCase;

class AvaliationBciInfoTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function testBciInfoMapsMixedUnderweightProfileToOver1AndIgnoresMissingMetrics(): void
    {
        /** @var Avaliation&\Mockery\LegacyMockInterface $avaliation */
        $avaliation = Mockery::mock(Avaliation::class)->makePartial();

        $avaliation->shouldReceive('getBmiInfo')->andReturn($this->metricInfo(2, 16.44));
        $avaliation->shouldReceive('getBodyFatInfo')->andReturn($this->metricInfo(4, 27.91));
        $avaliation->shouldReceive('getWaistToHipRatioInfo')->andReturn($this->metricInfo(3, null));
        $avaliation->shouldReceive('getVisceralFatInfo')->andReturn($this->metricInfo(2, 11.4));

        $info = $avaliation->getBciInfo();

        $this->assertEquals(3, $info[Constants::FI_RANK]);
        $this->assertEquals(__('messages.components.avaliationReport.rankBarLabel3'), $info[Constants::FI_RANK_LABEL]);
    }

    public function testBciInfoReturnsDataNotFilledWhenNoMetricCanBeCalculated(): void
    {
        /** @var Avaliation&\Mockery\LegacyMockInterface $avaliation */
        $avaliation = Mockery::mock(Avaliation::class)->makePartial();

        $avaliation->shouldReceive('getBmiInfo')->andReturn($this->metricInfo(-1, null));
        $avaliation->shouldReceive('getBodyFatInfo')->andReturn($this->metricInfo(-1, null));
        $avaliation->shouldReceive('getWaistToHipRatioInfo')->andReturn($this->metricInfo(-1, null));
        $avaliation->shouldReceive('getVisceralFatInfo')->andReturn($this->metricInfo(-1, null));

        $info = $avaliation->getBciInfo();

        $this->assertEquals(-1, $info[Constants::FI_RANK]);
        $this->assertEquals(__('messages.components.avaliationReport.dataNotFilled'), $info[Constants::FI_RANK_LABEL]);
        $this->assertEquals(Constants::RANK_COLOR_DEFAULT, $info[Constants::FI_RANK_COLOR]);
    }

    public function testProgressContextInfoSeparatesWeightStatusAndRiskStatus(): void
    {
        /** @var Avaliation&\Mockery\LegacyMockInterface $avaliation */
        $avaliation = Mockery::mock(Avaliation::class)->makePartial();

        $avaliation->shouldReceive('getEstimatedIdealWeightInfo')->andReturn($this->metricInfo(1, 62.71));
        $avaliation->shouldReceive('getBmiInfo')->andReturn($this->metricInfo(2, 16.44));
        $avaliation->shouldReceive('getBodyFatInfo')->andReturn($this->metricInfo(4, 27.91));
        $avaliation->shouldReceive('getWaistToHipRatioInfo')->andReturn($this->metricInfo(3, null));
        $avaliation->shouldReceive('getVisceralFatInfo')->andReturn($this->metricInfo(2, 11.4));

        $context = $avaliation->getProgressContextInfo();

        $this->assertEquals(__('messages.components.avaliationReport.progressWeightBelow'), $context['weightStatusLabel']);
        $this->assertEquals(__('messages.components.avaliationReport.progressRiskModerate'), $context['riskStatusLabel']);
    }

    private function metricInfo(int $rank, ?float $fieldValue): array
    {
        return [
            Constants::FI_FIELD_LABEL => '',
            Constants::FI_FIELD_VALUE => $fieldValue,
            Constants::FI_FIELD_SUFFIX => '',
            Constants::FI_RANK => $rank,
            Constants::FI_RANK_IDX => max(-1, $rank - 1),
            Constants::FI_RANK_LABEL => '',
            Constants::FI_RANK_COLOR => Constants::RANK_COLOR_DEFAULT,
            Constants::FI_IDEAL_MIN => '',
            Constants::FI_IDEAL_MAX => '',
            Constants::FI_IDEAL_LABEL => '',
        ];
    }
}
