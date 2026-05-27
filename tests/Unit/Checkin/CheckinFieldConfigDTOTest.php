<?php

namespace Tests\Unit\Checkin;

use App\DTO\Checkin\CheckinFieldConfigDTO;
use App\Enums\CheckinFieldType;
use Tests\TestCase;

class CheckinFieldConfigDTOTest extends TestCase
{
    public function testDtoSupportsFluentSettersAndArrayRoundTrip(): void
    {
        $dto = (new CheckinFieldConfigDTO())
            ->setFieldType(CheckinFieldType::SELECT)
            ->setFieldKey('energy_level')
            ->setLabel('How is your energy?')
            ->setRequired(true)
            ->setOptions([
                'great' => 'Great',
                'ok' => 'Ok',
            ]);

        $array = $dto->toArray();
        $this->assertSame(CheckinFieldType::SELECT, $array['field_type']);
        $this->assertSame('energy_level', $array['field_key']);
        $this->assertSame('How is your energy?', $array['label']);
        $this->assertTrue($array['required']);
        $this->assertSame([
            'great' => 'Great',
            'ok' => 'Ok',
        ], $array['options']);

        $roundTrip = CheckinFieldConfigDTO::fromArray($array);
        $this->assertSame($array, $roundTrip->toArray());
    }
}
