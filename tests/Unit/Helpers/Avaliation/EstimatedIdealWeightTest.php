<?php

namespace Tests\Unit\Helpers\Avaliation;

use App\Helpers\Avaliation\EstimatedIdealWeight;
use App\Helpers\Constants;
use App\Models\Avaliation;
use App\Models\Client;
use Mockery;
use Tests\TestCase;

class EstimatedIdealWeightTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function testEstimatedIdealWeightUsesHealthyRangeMidpoint(): void
    {
        $avaliation = $this->createAvaliationMock(heightCm: 170, weightKg: 80);

        $helper = new EstimatedIdealWeight($avaliation);

        $this->assertEqualsWithDelta(62.71, $helper->getFieldValue(), 0.01);
    }

    public function testIdealRangeIsDerivedFromHeight(): void
    {
        $avaliation = $this->createAvaliationMock(heightCm: 170, weightKg: 80);

        $helper = new EstimatedIdealWeight($avaliation);

        $this->assertEqualsWithDelta(59.57, $helper->getMinIdealValue(), 0.01);
        $this->assertEqualsWithDelta(65.85, $helper->getMaxIdealValue(), 0.01);
    }

    public function testRankIsBelowWhenCurrentWeightIsBelowReferenceRange(): void
    {
        $avaliation = $this->createAvaliationMock(heightCm: 170, weightKg: 50);

        $helper = new EstimatedIdealWeight($avaliation);

        $this->assertEquals(1, $helper->getRankNbr());
    }

    public function testRankIsWithinWhenCurrentWeightIsWithinReferenceRange(): void
    {
        $avaliation = $this->createAvaliationMock(heightCm: 170, weightKg: 63);

        $helper = new EstimatedIdealWeight($avaliation);

        $this->assertEquals(2, $helper->getRankNbr());
    }

    public function testRankIsAboveWhenCurrentWeightIsAboveReferenceRange(): void
    {
        $avaliation = $this->createAvaliationMock(heightCm: 170, weightKg: 80);

        $helper = new EstimatedIdealWeight($avaliation);

        $this->assertEquals(3, $helper->getRankNbr());
    }

    public function testGetFieldInfoReturnsExpectedStructure(): void
    {
        $avaliation = $this->createAvaliationMock(heightCm: 170, weightKg: 80);

        $helper = new EstimatedIdealWeight($avaliation);
        $info = $helper->getFieldInfo();

        $this->assertArrayHasKey(Constants::FI_FIELD_LABEL, $info);
        $this->assertArrayHasKey(Constants::FI_FIELD_VALUE, $info);
        $this->assertArrayHasKey(Constants::FI_RANK_LABEL, $info);
        $this->assertArrayHasKey(Constants::FI_IDEAL_LABEL, $info);
        $this->assertEquals(__('messages.components.avaliationReport.estimatedIdealWeightAbove'), $info[Constants::FI_RANK_LABEL]);
        $this->assertEquals('59,6kg - 65,8kg (atual 80,0kg)', $info[Constants::FI_IDEAL_LABEL]);
    }

    public function testCannotCalculateWithoutHeight(): void
    {
        $avaliation = $this->createAvaliationMock(heightCm: null, weightKg: 80);

        $helper = new EstimatedIdealWeight($avaliation);

        $this->assertEquals(Constants::RETURN_INT_CANT_CALCULATE, $helper->getFieldValue());
    }

    /**
     * @return Avaliation&\Mockery\LegacyMockInterface
     */
    private function createAvaliationMock(?float $heightCm, ?float $weightKg)
    {
        $client = Mockery::mock(Client::class);
        $client->shouldReceive('isMale')->andReturn(true);
        $client->shouldReceive('isFemale')->andReturn(false);

        /** @var Avaliation&\Mockery\LegacyMockInterface $avaliation */
        $avaliation = Mockery::mock(Avaliation::class)->makePartial();
        $avaliation->height_cm = $heightCm;
        $avaliation->weight_kg = $weightKg;
        $avaliation->client = $client;

        return $avaliation;
    }
}
