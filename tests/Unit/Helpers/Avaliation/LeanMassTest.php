<?php

namespace Tests\Unit\Helpers\Avaliation;

use App\Helpers\Avaliation\LeanMass;
use App\Helpers\Constants;
use App\Models\Avaliation;
use App\Models\Client;
use Mockery;
use Tests\TestCase;

class LeanMassTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    /**
     * Test Lean Mass calculation: weight - fat_mass_kg
     */
    public function testLeanMassCalculation(): void
    {
        // Client: 76.55kg, 40.6% body fat
        // Fat mass: 76.55 * 0.406 = 31.0783 kg
        // Lean mass: 76.55 - 31.0783 = 45.4717 kg
        $avaliation = $this->createAvaliationMock(76.55, 40.6);

        $leanMass = new LeanMass($avaliation);
        $this->assertEqualsWithDelta(45.47, $leanMass->getFieldValue(), 0.01);
    }

    /**
     * Test Lean Mass with high muscle percentage
     */
    public function testLeanMassCalculationHighMuscle(): void
    {
        // Client: 80kg, 18% body fat
        // Fat mass: 80 * 0.18 = 14.4 kg
        // Lean mass: 80 - 14.4 = 65.6 kg
        $avaliation = $this->createAvaliationMock(80, 18);

        $leanMass = new LeanMass($avaliation);
        $this->assertEquals(65.6, $leanMass->getFieldValue());
    }

    /**
     * Test ideal values for men (80-90% lean mass range)
     */
    public function testIdealValuesForMen(): void
    {
        // 70kg man
        $avaliation = $this->createAvaliationMock(70, 15, isMale: true);

        $leanMass = new LeanMass($avaliation);
        // Ideal for men: 80-90% of weight = 56-63kg
        $this->assertEquals(56.0, $leanMass->getMinIdealValue());
        $this->assertEquals(63.0, $leanMass->getMaxIdealValue());
    }

    /**
     * Test ideal values for women (75-85% lean mass range)
     */
    public function testIdealValuesForWomen(): void
    {
        // 60kg woman
        $avaliation = $this->createAvaliationMock(60, 21, isMale: false);

        $leanMass = new LeanMass($avaliation);
        // Ideal for women: 75-85% of weight = 45-51kg
        $this->assertEquals(45.0, $leanMass->getMinIdealValue());
        $this->assertEquals(51.0, $leanMass->getMaxIdealValue());
    }

    public function testRankingBelowExpectedRange(): void
    {
        $avaliation = $this->createAvaliationMock(76.55, 40.6, isMale: false);

        $leanMass = new LeanMass($avaliation);
        $this->assertEquals(1, $leanMass->getRankNbr());
    }

    public function testRankingWithinExpectedRange(): void
    {
        $avaliation = $this->createAvaliationMock(60, 25, isMale: false);

        $leanMass = new LeanMass($avaliation);
        $this->assertEquals(2, $leanMass->getRankNbr());
    }

    public function testRankingAboveExpectedRange(): void
    {
        $avaliation = $this->createAvaliationMock(85, 8, isMale: true);

        $leanMass = new LeanMass($avaliation);
        $this->assertEquals(3, $leanMass->getRankNbr());
    }

    /**
     * Test field label formatting
     */
    public function testFieldLabel(): void
    {
        $avaliation = $this->createAvaliationMock(76.55, 40.6);

        $leanMass = new LeanMass($avaliation);
        $label = $leanMass->getFieldLabel();

        $this->assertStringContainsString('45', $label);
    }

    /**
     * Test field suffix
     */
    public function testFieldSuffix(): void
    {
        $avaliation = $this->createAvaliationMock(70, 20);

        $leanMass = new LeanMass($avaliation);
        $this->assertEquals('kg', $leanMass->getFieldSuffix());
    }

    /**
     * Test getFieldInfo() returns complete data structure
     */
    public function testGetFieldInfo(): void
    {
        $avaliation = $this->createAvaliationMock(60, 25, isMale: false);

        $leanMass = new LeanMass($avaliation);
        $info = $leanMass->getFieldInfo();

        $this->assertIsArray($info);
        $this->assertArrayHasKey(Constants::FI_FIELD_LABEL, $info);
        $this->assertArrayHasKey(Constants::FI_FIELD_VALUE, $info);
        $this->assertArrayHasKey(Constants::FI_RANK, $info);
        $this->assertArrayHasKey(Constants::FI_RANK_LABEL, $info);
        $this->assertArrayHasKey(Constants::FI_IDEAL_LABEL, $info);
    }

    /**
     * Helper to create a mocked Avaliation with known values
     */
    private function createAvaliationMock(
        float $weight,
        float $bodyFatPerc,
        bool $isMale = false
    ): Avaliation {
        $fatMassKg = $weight * ($bodyFatPerc / 100);
        $leanMassKg = $weight - $fatMassKg;

        $client = Mockery::mock(Client::class);
        $client->shouldReceive('isMale')->andReturn($isMale);

        /** @var Avaliation&\Mockery\LegacyMockInterface $avaliation */
        $avaliation = Mockery::mock(Avaliation::class)->makePartial();
        $avaliation->weight_kg = $weight;
        $avaliation->client = $client;
        $avaliation->shouldReceive('getBodyFatPerc')->andReturn($bodyFatPerc);
        $avaliation->shouldReceive('getFatMassKg')->andReturn($fatMassKg);
        $avaliation->shouldReceive('getLeanMassKg')->andReturn($leanMassKg);
        $avaliation->shouldReceive('getFormattedLeanMass')->andReturn(
            round($leanMassKg, 2) . ' kg'
        );

        return $avaliation;
    }
}
