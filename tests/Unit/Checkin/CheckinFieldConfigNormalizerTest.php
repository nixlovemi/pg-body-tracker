<?php

namespace Tests\Unit\Checkin;

use App\Enums\CheckinFieldType;
use App\Services\Checkin\CheckinFieldConfigNormalizer;
use Tests\TestCase;

class CheckinFieldConfigNormalizerTest extends TestCase
{
    public function testNormalizerAppliesFieldRulesForVisibilityAndOptions(): void
    {
        $normalized = app(CheckinFieldConfigNormalizer::class)->normalize([
            [
                'field_type' => CheckinFieldType::WEIGHT,
                'field_key' => 'weight_kg',
                'label' => 'Weight',
                'required' => true,
            ],
            [
                'field_type' => CheckinFieldType::YES_NO,
                'label' => 'Did you follow the plan?',
                'required' => true,
                'options' => ['should', 'be', 'ignored'],
            ],
            [
                'field_type' => CheckinFieldType::SELECT,
                'label' => 'How was your energy?',
                'required' => false,
                'options' => ['great', 'ok'],
            ],
        ]);

        $this->assertCount(2, $normalized);

        $this->assertSame(CheckinFieldType::YES_NO, $normalized[0]['field_type']);
        $this->assertSame('did_you_follow_the_plan', $normalized[0]['field_key']);
        $this->assertSame([], $normalized[0]['options']);

        $this->assertSame(CheckinFieldType::SELECT, $normalized[1]['field_type']);
        $this->assertSame([
            'great' => 'great',
            'ok' => 'ok',
        ], $normalized[1]['options']);
    }
}
