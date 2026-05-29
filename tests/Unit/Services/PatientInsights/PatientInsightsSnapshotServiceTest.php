<?php

namespace Tests\Unit\Services\PatientInsights;

use App\Helpers\CheckinFields\Fields\WeightField;
use App\Models\Avaliation;
use App\Models\AvaliationCheckinField;
use App\Models\CheckinConfig;
use App\Models\Client;
use App\Models\Goal;
use App\Models\User;
use App\Services\PatientInsights\PatientInsightsSnapshotService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PatientInsightsSnapshotServiceTest extends TestCase
{
    use DatabaseTransactions;

    public function testPremiumCardIncludesAlignedWeightVariabilityReason(): void
    {
        $now = Carbon::create(2026, 6, 5, 10, 0, 0);

        [$user, $client] = $this->createManagerAndClient();
        $this->actingAs($user, 'web');

        DB::table('goals')->insert([
            'client_id' => $client->id,
            'objective' => Goal::OBJECTIVE_WEIGHT_LOSS,
            'initial_weight_kg' => 83.0,
            'target_weight_kg' => 75.0,
            'deadline' => $now->copy()->addDays(60)->format('Y-m-d'),
            'created_at' => $now->copy()->subDays(20),
            'updated_at' => $now->copy()->subDays(20),
        ]);

        $rows = [
            ['daysAgo' => 28, 'weight' => 84.1],
            ['daysAgo' => 10, 'weight' => 83.5],
            ['daysAgo' => 6, 'weight' => 83.0],
            ['daysAgo' => 3, 'weight' => 81.8],
            ['daysAgo' => 1, 'weight' => 82.2],
        ];

        $lastAvaliationId = null;
        foreach ($rows as $row) {
            $lastAvaliationId = DB::table('avaliations')->insertGetId([
                'client_id' => $client->id,
                'date' => $now->copy()->subDays((int) $row['daysAgo'])->format('Y-m-d'),
                'age' => $client->getAge(),
                'weight_kg' => (float) $row['weight'],
                'height_cm' => $client->height_cm,
                'calculate_perc_fat_by' => Avaliation::CALCULATE_PERC_FAT_BY_BIOIMPEDANCE,
            ]);
        }

        AvaliationCheckinField::create([
            'avaliation_id' => $lastAvaliationId,
            'field_class' => WeightField::class,
            'field_type' => 'weight',
            'field_key' => 'weight_kg',
            'response' => '82.2',
            'response_type' => AvaliationCheckinField::RESPONSE_TYPE_NUMBER,
            'field_meta' => [],
        ]);

        CheckinConfig::create([
            'client_id' => $client->id,
            'active' => true,
            'interval_days' => 7,
            'link_expires_hours' => 24,
            'last_checkin_sent_date' => $now->copy()->format('Y-m-d'),
            'unanswered_reminders_sent' => 0,
        ]);

        $card = app(PatientInsightsSnapshotService::class)->buildPremiumCard($client->fresh(), $now);
        $reasonKeys = array_map(function (array $reason): string {
            return (string) ($reason['key'] ?? '');
        }, (array) ($card['reasons'] ?? []));

        $this->assertContains('weight_variability_7d', $reasonKeys);
    }

    private function createManagerAndClient(): array
    {
        $user = User::factory()->create([
            'role' => User::ROLE_MANAGER,
            'active' => true,
            'confirmation' => true,
        ]);

        $client = Client::factory()->create([
            'user_id' => $user->id,
            'gender' => Client::GENDER_MALE,
            'birthdate' => '1996-05-27',
            'height_cm' => 178,
            'weight_kg' => 80,
        ]);

        return [$user, $client];
    }
}
