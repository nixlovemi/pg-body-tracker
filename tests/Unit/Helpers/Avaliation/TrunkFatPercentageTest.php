<?php

namespace Tests\Unit\Helpers\Avaliation;

use App\Helpers\Avaliation\TrunkFatPercentage;
use App\Helpers\Constants;
use App\Models\Avaliation;
use App\Models\Client;
use Mockery;
use Tests\TestCase;

class TrunkFatPercentageTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    /**
     * Test that trunk fat percentage is retrieved from database value
     */
    public function testTrunkFatPercentageRetrieval(): void
    {
        $avaliation = $this->createAvaliationMock(18.5, isMale: true);

        $trunkFat = new TrunkFatPercentage($avaliation);
        $this->assertEquals(18.5, $trunkFat->getFieldValue());
    }

    /**
     * Test ideal values for men (10-20% trunk fat)
     */
    public function testIdealValuesForMen(): void
    {
        $avaliation = $this->createAvaliationMock(15.0, isMale: true);

        $trunkFat = new TrunkFatPercentage($avaliation);
        $this->assertEquals(10, $trunkFat->getMinIdealValue());
        $this->assertEquals(20, $trunkFat->getMaxIdealValue());
    }

    /**
     * Test ideal values for women (15-25% trunk fat)
     */
    public function testIdealValuesForWomen(): void
    {
        $avaliation = $this->createAvaliationMock(22.0, isMale: false);

        $trunkFat = new TrunkFatPercentage($avaliation);
        $this->assertEquals(15, $trunkFat->getMinIdealValue());
        $this->assertEquals(25, $trunkFat->getMaxIdealValue());
    }

    /**
     * Test ranking for low trunk fat (rank 1 for man at 12%)
     */
    public function testRankingLowMan(): void
    {
        // Man with 12% trunk fat (< 15% threshold)
        $avaliation = $this->createAvaliationMock(12.0, isMale: true);

        $trunkFat = new TrunkFatPercentage($avaliation);
        $this->assertEquals(1, $trunkFat->getRankNbr());
    }

    /**
     * Test ranking for ideal trunk fat (rank 2 for man at 18%)
     */
    public function testRankingIdealMan(): void
    {
        // Man with 18% trunk fat (15-22% range)
        $avaliation = $this->createAvaliationMock(18.0, isMale: true);

        $trunkFat = new TrunkFatPercentage($avaliation);
        $this->assertEquals(2, $trunkFat->getRankNbr());
    }

    /**
     * Test ranking for high trunk fat (rank 3 for man at 26%)
     */
    public function testRankingHighMan(): void
    {
        // Man with 26% trunk fat (22-30% range)
        $avaliation = $this->createAvaliationMock(26.0, isMale: true);

        $trunkFat = new TrunkFatPercentage($avaliation);
        $this->assertEquals(3, $trunkFat->getRankNbr());
    }

    /**
     * Test ranking for very high trunk fat (rank 4 for woman at 38%)
     */
    public function testRankingVeryHighWoman(): void
    {
        // Woman with 38% trunk fat (> 36% threshold)
        $avaliation = $this->createAvaliationMock(38.0, isMale: false);

        $trunkFat = new TrunkFatPercentage($avaliation);
        $this->assertEquals(4, $trunkFat->getRankNbr());
    }

    /**
     * Test field suffix
     */
    public function testFieldSuffix(): void
    {
        $avaliation = $this->createAvaliationMock(20.0, isMale: true);

        $trunkFat = new TrunkFatPercentage($avaliation);
        $this->assertEquals('%', $trunkFat->getFieldSuffix());
    }

    /**
     * Test getFieldInfo() returns complete data structure
     */
    public function testGetFieldInfo(): void
    {
        $avaliation = $this->createAvaliationMock(18.5, isMale: true);

        $trunkFat = new TrunkFatPercentage($avaliation);
        $info = $trunkFat->getFieldInfo();

        $this->assertIsArray($info);
        $this->assertArrayHasKey(Constants::FI_FIELD_LABEL, $info);
        $this->assertArrayHasKey(Constants::FI_FIELD_VALUE, $info);
        $this->assertArrayHasKey(Constants::FI_RANK, $info);
        $this->assertArrayHasKey(Constants::FI_RANK_LABEL, $info);
        $this->assertArrayHasKey(Constants::FI_IDEAL_LABEL, $info);
    }

    /**
     * Test handling of null trunk fat (not calculated)
     */
    public function testNullTrunkFatPercentage(): void
    {
        $client = Mockery::mock(Client::class);
        $client->shouldReceive('isMale')->andReturn(true);

        /** @var Avaliation&\Mockery\LegacyMockInterface $avaliation */
        $avaliation = Mockery::mock(Avaliation::class)->makePartial();
        $avaliation->trunk_fat_perc = null;
        $avaliation->client = $client;

        $trunkFat = new TrunkFatPercentage($avaliation);
        $this->assertEquals(Constants::RETURN_INT_CANT_CALCULATE, $trunkFat->getFieldValue());
    }

    /**
     * Helper to create a mocked Avaliation with known values
     *
     * @return Avaliation&\Mockery\LegacyMockInterface
     */
    private function createAvaliationMock(
        ?float $trunkFatPerc = null,
        bool $isMale = false,
        ?float $waistCirc = null,
        ?float $abdomenCirc = null,
        ?float $heightCm = null,
        ?float $bodyFatPerc = null,
        ?float $weight = null
    ) {
        $client = Mockery::mock(Client::class);
        $client->shouldReceive('isMale')->andReturn($isMale);
        $client->shouldReceive('isFemale')->andReturn(!$isMale);

        /** @var Avaliation&\Mockery\LegacyMockInterface $avaliation */
        $avaliation = Mockery::mock(Avaliation::class)->makePartial();
        $avaliation->trunk_fat_perc = $trunkFatPerc;
        $avaliation->waist_circ_cm = $waistCirc;
        $avaliation->abdomen_circ_cm = $abdomenCirc;
        $avaliation->height_cm = $heightCm;
        $avaliation->body_fat_perc = $bodyFatPerc;
        $avaliation->weight_kg = $weight;
        $avaliation->client = $client;

        // Mock getBodyFatPerc to return body_fat_perc if set
        if ($bodyFatPerc !== null) {
            $avaliation->shouldReceive('getBodyFatPerc')
                ->andReturn($bodyFatPerc);
        } else {
            $avaliation->shouldReceive('getBodyFatPerc')
                ->andReturn(Constants::RETURN_INT_CANT_CALCULATE);
        }

        return $avaliation;
    }

    /**
     * Test estimating trunk fat using Katch-McArdle formula (waist/abdomen/height)
     */
    public function testEstimateTrunkFatPercByKatchMcArdle(): void
    {
        // Male with waist 85cm, abdomen 90cm, height 180cm
        $avaliation = $this->createAvaliationMock(
            trunkFatPerc: null,
            isMale: true,
            waistCirc: 85.0,
            abdomenCirc: 90.0,
            heightCm: 180.0
        );

        $trunkFat = new TrunkFatPercentage($avaliation);
        $value = $trunkFat->getFieldValue();

        // Value should be calculated and not be CANT_CALCULATE
        $this->assertNotEquals(Constants::RETURN_INT_CANT_CALCULATE, $value);
        // Should be within realistic range
        $this->assertGreaterThan(0, $value);
        $this->assertLessThan(100, $value);
    }

    /**
     * Test estimating trunk fat using body fat percentage proportion
     */
    public function testEstimateTrunkFatPercByBodyFatPercentage(): void
    {
        // Male with 25% body fat (tronco ~45% da gordura)
        $avaliation = $this->createAvaliationMock(
            trunkFatPerc: null,
            isMale: true,
            bodyFatPerc: 25.0
        );

        $trunkFat = new TrunkFatPercentage($avaliation);
        $value = $trunkFat->getFieldValue();

        // For males, trunk should be ~45% of total body fat
        // 25% * 0.45 = 11.25%
        $this->assertGreaterThan(10, $value);
        $this->assertLessThan(13, $value);
    }

    /**
     * Test cascade: direct value is preferred
     */
    public function testGetTrunkFatPercCascadePrefersDirect(): void
    {
        $avaliation = $this->createAvaliationMock(
            trunkFatPerc: 18.5,
            isMale: true,
            waistCirc: 85.0,
            abdomenCirc: 90.0,
            heightCm: 180.0,
            bodyFatPerc: 25.0
        );

        $trunkFat = new TrunkFatPercentage($avaliation);
        // Should use the direct value (18.5) not estimated ones
        $this->assertEquals(18.5, $trunkFat->getFieldValue());
    }

    /**
     * Test cascade: Katch-McArdle is used if direct is missing
     */
    public function testGetTrunkFatPercCascadeUsesKatchMcArdle(): void
    {
        $avaliation = $this->createAvaliationMock(
            trunkFatPerc: null,
            isMale: true,
            waistCirc: 85.0,
            abdomenCirc: 90.0,
            heightCm: 180.0,
            bodyFatPerc: 25.0
        );

        $trunkFat = new TrunkFatPercentage($avaliation);
        $value = $trunkFat->getFieldValue();

        // Should use Katch-McArdle estimation
        $this->assertNotEquals(Constants::RETURN_INT_CANT_CALCULATE, $value);
        // Should NOT be exactly 11.25 (which would be body_fat_perc method)
        $this->assertNotEquals(11.25, $value);
    }

    /**
     * Test cascade: falls back to body fat percentage method
     */
    public function testGetTrunkFatPercCascadeUsesBodyFatPercentage(): void
    {
        $avaliation = $this->createAvaliationMock(
            trunkFatPerc: null,
            isMale: true,
            // No circunferências
            waistCirc: null,
            abdomenCirc: null,
            heightCm: null,
            bodyFatPerc: 25.0
        );

        $trunkFat = new TrunkFatPercentage($avaliation);
        $value = $trunkFat->getFieldValue();

        // Should use body_fat_perc method: 25% * 0.45 = 11.25
        $this->assertEquals(11.25, $value);
    }
}
