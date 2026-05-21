<?php

namespace Tests\Feature\Helpers\Avaliation;

use Tests\TestCase;
use App\Helpers\Avaliation\AvaliationFieldInfoAbstract;
use App\Models\Client;
use App\Models\User;
use App\Models\Avaliation;
use App\Helpers\Constants;

class DummyAvaliationFieldInfo extends AvaliationFieldInfoAbstract
{
    public function getFieldSuffix(): string
    {
        return 'kg';
    }

    public function getFieldValue(): float|int
    {
        return 70;
    }

    public function getFieldLabel(): string
    {
        return '70 kg';
    }

    public function getManRanking(): array
    {
        return [60, 80, 100];
    }

    public function getWomanRanking(): array
    {
        return [50, 70, 90];
    }

    public function getRankingLabels(): array
    {
        return ['Ruim', 'Bom', 'Ótimo'];
    }

    public function getRankingColors(): array
    {
        return ['#ff0000', '#00ff00', '#0000ff'];
    }
}

class AvaliationFieldInfoAbstractTest extends TestCase
{
    protected function makeAvaliationWithClient($isMale = true)
    {
        /** @var User $user */
        $user = User::factory()->create([
            'role' => User::ROLE_MANAGER,
            'active' => true,
            'confirmation' => true,
        ]);
        $this->actingAs($user, 'web');

        /** @var Client $client */
        $client = Client::factory()->create([
            'user_id' => $user->id,
            'gender' => $isMale ? Client::GENDER_MALE : Client::GENDER_FEMALE,
            'birthdate' => '1990-01-01',
            'height_cm' => 180,
            'weight_kg' => 80,
        ]);

        /** @var Avaliation $avaliation */
        $avaliation = Avaliation::factory()->create([
            'client_id' => $client->id,
            'date' => now()->subDay()->format('Y-m-d'),
        ]);

        return $avaliation;
    }

    public function testGetFieldInfoForMale()
    {
        $avaliation = $this->makeAvaliationWithClient(true);
        $fieldInfo = new DummyAvaliationFieldInfo($avaliation);

        $info = $fieldInfo->getFieldInfo();

        $this->assertEquals('70 kg', $info[Constants::FI_FIELD_LABEL]);
        $this->assertEquals(70, $info[Constants::FI_FIELD_VALUE]);
        $this->assertEquals('kg', $info[Constants::FI_FIELD_SUFFIX]);
        $this->assertEquals(2, $info[Constants::FI_RANK]); // 70 < 80 (2nd rank)
        $this->assertEquals(1, $info[Constants::FI_RANK_IDX]);
        $this->assertEquals('Bom', $info[Constants::FI_RANK_LABEL]);
        $this->assertEquals('#00ff00', $info[Constants::FI_RANK_COLOR]);
        $this->assertStringContainsString('kg', $info[Constants::FI_IDEAL_MIN]);
        $this->assertStringContainsString('kg', $info[Constants::FI_IDEAL_MAX]);
        $this->assertStringContainsString('kg', $info[Constants::FI_IDEAL_LABEL]);
    }

    public function testGetFieldInfoForFemale()
    {
        $avaliation = $this->makeAvaliationWithClient(false);
        $fieldInfo = new DummyAvaliationFieldInfo($avaliation);

        $info = $fieldInfo->getFieldInfo();

        $this->assertEquals('70 kg', $info[Constants::FI_FIELD_LABEL]);
        $this->assertEquals(70, $info[Constants::FI_FIELD_VALUE]);
        $this->assertEquals('kg', $info[Constants::FI_FIELD_SUFFIX]);
        $this->assertEquals(3, $info[Constants::FI_RANK]);
        $this->assertEquals(2, $info[Constants::FI_RANK_IDX]);
        $this->assertEquals('Ótimo', $info[Constants::FI_RANK_LABEL]);
        $this->assertEquals('#0000ff', $info[Constants::FI_RANK_COLOR]);
        $this->assertStringContainsString('kg', $info[Constants::FI_IDEAL_MIN]);
        $this->assertStringContainsString('kg', $info[Constants::FI_IDEAL_MAX]);
        $this->assertStringContainsString('kg', $info[Constants::FI_IDEAL_LABEL]);
    }

    public function testGetFieldInfoCantCalculate()
    {
        $this->expectException(\LogicException::class);

        $avaliation = $this->makeAvaliationWithClient(true);
        $fieldInfo = new class($avaliation) extends AvaliationFieldInfoAbstract {
            public function getFieldSuffix(): string { return 'kg'; }
            public function getFieldValue(): float|int { return Constants::RETURN_INT_CANT_CALCULATE; }
            public function getFieldLabel(): string { return 'N/A'; }
            public function getManRanking(): array { return []; }
            public function getWomanRanking(): array { return []; }
            public function getRankingLabels(): array { return []; }
            public function getRankingColors(): array { return []; }
        };

        $info = $fieldInfo->getFieldInfo();
    }

    public function testThrowsIfIdealValuesNotSet()
    {
        $this->expectException(\LogicException::class);
        $avaliation = $this->makeAvaliationWithClient(true);

        // Create a dummy class that does not set ideal values
        $dummy = new class($avaliation) extends AvaliationFieldInfoAbstract {
            public function getFieldSuffix(): string { return 'kg'; }
            public function getFieldValue(): float|int { return 70; }
            public function getFieldLabel(): string { return '70 kg'; }
            public function getManRanking(): array { return []; }
            public function getWomanRanking(): array { return []; }
            public function getRankingLabels(): array { return []; }
            public function getRankingColors(): array { return []; }
        };
        $dummy->getFieldInfo();
    }
}
