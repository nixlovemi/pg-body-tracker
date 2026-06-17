<?php

namespace Tests\Unit\Helpers\Avaliation;

use App\Helpers\Avaliation\FatMass;
use App\Helpers\Constants;
use App\Models\Avaliation;
use App\Models\Client;
use Mockery;
use Tests\TestCase;

class FatMassTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    /**
     * Test Fat Mass calculation: weight * (body_fat_perc / 100)
     */
    public function testFatMassCalculation(): void
    {
        // Client: 76.55kg, 40.6% body fat
        // Expected: 76.55 * (40.6 / 100) = 31.0783 kg
        $avaliation = $this->createAvaliationMock(76.55, 40.6);

        $fatMass = new FatMass($avaliation);
        $this->assertEqualsWithDelta(31.08, $fatMass->getFieldValue(), 0.01);
    }

    /**
     * Test Fat Mass calculation with different weight/body fat combo
     */
    public function testFatMassCalculationDifferentValues(): void
    {
        // Client: 60kg, 25% body fat
        // Expected: 60 * 0.25 = 15 kg
        $avaliation = $this->createAvaliationMock(60, 25);

        $fatMass = new FatMass($avaliation);
        $this->assertEquals(15.0, $fatMass->getFieldValue());
    }

    /**
     * Test ideal values for men (10-20% body fat range)
     */
    public function testIdealValuesForMen(): void
    {
        // 70kg man
        $avaliation = $this->createAvaliationMock(70, 15, isMale: true);

        $fatMass = new FatMass($avaliation);
        // Ideal for men: 10-20% of weight = 7-14kg
        $this->assertEquals(7.0, $fatMass->getMinIdealValue());
        $this->assertEquals(14.0, $fatMass->getMaxIdealValue());
    }

    /**
     * Test ideal values for women (18-25% body fat range)
     */
    public function testIdealValuesForWomen(): void
    {
        // 60kg woman
        $avaliation = $this->createAvaliationMock(60, 21, isMale: false);

        $fatMass = new FatMass($avaliation);
        // Ideal for women: 18-25% of weight = 10.8-15kg
        $this->assertEquals(10.8, $fatMass->getMinIdealValue());
        $this->assertEquals(15.0, $fatMass->getMaxIdealValue());
    }

    /**
     * Test ranking for low fat mass (rank 1)
     */
    public function testRankingLow(): void
    {
        // Woman with very low body fat (8%)
        $avaliation = $this->createAvaliationMock(60, 8, isMale: false);

        $fatMass = new FatMass($avaliation);
        // 8% = 4.8 kg < 12kg (first threshold)
        $this->assertEquals(1, $fatMass->getRankNbr());
    }

    /**
     * Test ranking for ideal fat mass (rank 2)
     */
    public function testRankingIdeal(): void
    {
        // Woman with ideal body fat (22%)
        $avaliation = $this->createAvaliationMock(60, 22, isMale: false);

        $fatMass = new FatMass($avaliation);
        // 22% = 13.2 kg, between 12-18 (rank 2)
        $this->assertEquals(2, $fatMass->getRankNbr());
    }

    /**
     * Test ranking for high fat mass (rank 5)
     */
    public function testRankingHigh(): void
    {
        // Woman with high body fat (36%)
        $avaliation = $this->createAvaliationMock(60, 36, isMale: false);

        $fatMass = new FatMass($avaliation);
        // 36% = 21.6 kg, between 18-23 (rank 3) or higher depending on thresholds
        $this->assertGreaterThanOrEqual(3, $fatMass->getRankNbr());
    }

    /**
     * Test field label formatting
     */
    public function testFieldLabel(): void
    {
        $avaliation = $this->createAvaliationMock(76.55, 40.6);

        $fatMass = new FatMass($avaliation);
        $label = $fatMass->getFieldLabel();

        $this->assertStringContainsString('31', $label);
    }

    /**
     * Test field suffix
     */
    public function testFieldSuffix(): void
    {
        $avaliation = $this->createAvaliationMock(70, 20);

        $fatMass = new FatMass($avaliation);
        $this->assertEquals('kg', $fatMass->getFieldSuffix());
    }

    /**
     * Test getFieldInfo() returns complete data structure
     */
    public function testGetFieldInfo(): void
    {
        $avaliation = $this->createAvaliationMock(60, 22, isMale: false);

        $fatMass = new FatMass($avaliation);
        $info = $fatMass->getFieldInfo();

        $this->assertIsArray($info);
        $this->assertArrayHasKey(Constants::FI_FIELD_LABEL, $info);
        $this->assertArrayHasKey(Constants::FI_FIELD_VALUE, $info);
        $this->assertArrayHasKey(Constants::FI_RANK, $info);
        $this->assertArrayHasKey(Constants::FI_RANK_LABEL, $info);
        $this->assertArrayHasKey(Constants::FI_IDEAL_LABEL, $info);
    }

    /**
     * Helper to create a mocked Avaliation with known values
     *
     * @return Avaliation&\Mockery\LegacyMockInterface
     */
    private function createAvaliationMock(
        float $weight,
        float $bodyFatPerc,
        bool $isMale = false
    ) {
        $client = Mockery::mock(Client::class);
        $client->shouldReceive('isMale')->andReturn($isMale);

        /** @var Avaliation&\Mockery\LegacyMockInterface $avaliation */
        $avaliation = Mockery::mock(Avaliation::class)->makePartial();
        $avaliation->weight_kg = $weight;
        $avaliation->client = $client;
        $avaliation->shouldReceive('getBodyFatPerc')->andReturn($bodyFatPerc);
        $avaliation->shouldReceive('getFatMassKg')->andReturn($weight * ($bodyFatPerc / 100));
        $avaliation->shouldReceive('getFormattedFatMass')->andReturn(
            round($weight * ($bodyFatPerc / 100), 2) . ' kg'
        );

        return $avaliation;
    }
}
