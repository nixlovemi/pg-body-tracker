<?php

namespace Tests\Unit\Presenters;

use App\Models\Avaliation;
use App\Presenters\AvaliationReportPresenter;
use Mockery;
use Tests\TestCase;

class AvaliationReportPresenterTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function testGetGraphDataIncludesFatMassAndLeanMassGraphs(): void
    {
        $graphData = AvaliationReportPresenter::getGraphData();
        $helpers = array_column($graphData, 'helperClass');

        $this->assertContains('App\\Helpers\\AvaliationGraph\\AvaliationFatMassGraphHelper', $helpers);
        $this->assertContains('App\\Helpers\\AvaliationGraph\\AvaliationLeanMassGraphHelper', $helpers);
        $this->assertContains('App\\Helpers\\AvaliationGraph\\AvaliationWaistAbdomenCircumferenceGraphHelper', $helpers);
        $this->assertContains('App\\Helpers\\AvaliationGraph\\AvaliationTrunkFatPercentageGraphHelper', $helpers);
        $this->assertContains('App\\Helpers\\AvaliationGraph\\AvaliationSkeletalMuscleGraphHelper', $helpers);
    }

    public function testGetGraphDataContainsTitlesForFatMassAndLeanMass(): void
    {
        $graphData = AvaliationReportPresenter::getGraphData();
        $titles = array_column($graphData, 'title');

        $this->assertContains(__('messages.components.avaliationReport.fatMass'), $titles);
        $this->assertContains(__('messages.components.avaliationReport.leanMass'), $titles);
        $this->assertContains(__('messages.components.avaliationReport.waistAbdomenCircumference'), $titles);
        $this->assertContains(__('messages.components.avaliationReport.trunkFatPercentage'), $titles);
        $this->assertContains(__('messages.components.avaliationReport.skeletalMuscle'), $titles);
    }

    public function testGetInfoCardsDataIncludesEstimatedIdealWeightCard(): void
    {
        $methods = [
            'getWeightInfo',
            'getEstimatedIdealWeightInfo',
            'getBodyFatInfo',
            'getFatMassInfo',
            'getLeanMassInfo',
            'getSkeletalMuscleInfo',
            'getTrunkFatPercentageInfo',
            'getBmiInfo',
            'getBodyWaterInfo',
            'getBoneMassInfo',
            'getVisceralFatInfo',
            'getWaistAbdomenCircumferenceInfo',
            'getWaistToHipRatioInfo',
            'getBodyAgeInfo',
            'getBasalMetabolismInfo',
        ];

        /** @var Avaliation&\Mockery\LegacyMockInterface $avaliation */
        $avaliation = Mockery::mock(Avaliation::class);
        foreach ($methods as $method) {
            $avaliation->shouldReceive($method)->andReturn([]);
        }

        $cards = AvaliationReportPresenter::getInfoCardsData($avaliation);

        $cardMethods = array_column($cards, 'method');
        $titles = array_column($cards, 'title');

        $this->assertContains('getEstimatedIdealWeightInfo', $cardMethods);
        $this->assertContains(__('messages.components.avaliationReport.estimatedIdealWeight'), $titles);
    }
}
