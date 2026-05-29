<?php

namespace Tests\Unit\Services\PatientInsights;

use App\Enums\PatientEvolutionStatus;
use App\Enums\PatientSignalLevel;
use App\Helpers\CheckinFields\Fields\WeightField;
use App\Models\Avaliation;
use App\Models\AvaliationCheckinField;
use App\Models\CheckinConfig;
use App\Models\Client;
use App\Models\Goal;
use App\Models\User;
use App\Services\PatientInsights\PatientSignalEngine;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PatientSignalEngineTest extends TestCase
{
    use DatabaseTransactions;

    public function testEngineEvaluatesEightInitialSignalsAndBuildsRiskSummary(): void
    {
        $now = Carbon::create(2026, 5, 28, 12, 0, 0);

        [$user, $client] = $this->createManagerAndClient();
        $this->actingAs($user, 'web');

        $this->seedAvaliations($client, $now);

        DB::table('goals')->insert([
            'client_id' => $client->id,
            'objective' => Goal::OBJECTIVE_WEIGHT_LOSS,
            'initial_weight_kg' => 80.0,
            'target_weight_kg' => 70.0,
            'deadline' => $now->copy()->addDays(10)->format('Y-m-d'),
            'created_at' => $now->copy()->subDays(30),
            'updated_at' => $now->copy()->subDays(30),
        ]);

        CheckinConfig::create([
            'client_id' => $client->id,
            'active' => true,
            'interval_days' => 7,
            'link_expires_hours' => 24,
            'last_checkin_date' => $now->copy()->subDays(20)->format('Y-m-d'),
            'last_checkin_sent_date' => $now->copy()->subDays(15)->format('Y-m-d'),
            'unanswered_reminders_sent' => 2,
        ]);

        $result = app(PatientSignalEngine::class)->evaluateForClient($client->fresh(), true, $now);

        $this->assertCount(8, $result['signals']);

        $signalKeys = array_map(function (array $signal): string {
            return (string) $signal['key'];
        }, $result['signals']);

        $this->assertEqualsCanonicalizing([
            'weight_trend_14d',
            'weight_trend_30d',
            'weight_variability_7d',
            'checkin_response_rate_30d',
            'days_since_checkin_response',
            'unanswered_reminders',
            'goal_progress_pace',
            'avaliation_frequency',
        ], $signalKeys);

        $signalsByKey = $this->indexSignalsByKey($result['signals']);

        $this->assertSignal($signalsByKey['weight_trend_14d'], PatientSignalLevel::RISK, 3, 2.59, [
            'percent_change' => 2.59,
        ]);
        $this->assertSignal($signalsByKey['weight_trend_30d'], PatientSignalLevel::RISK, 3, 3.87, [
            'percent_change' => 3.87,
        ]);
        $this->assertSignal($signalsByKey['weight_variability_7d'], PatientSignalLevel::ATTENTION, 1, 0.6, [
            'variability_percent' => 0.6,
        ]);
        $this->assertSignal($signalsByKey['checkin_response_rate_30d'], PatientSignalLevel::RISK, 3, 25.0, [
            'rate_percent' => 25.0,
            'responses' => 1,
            'expected' => 4,
        ]);
        $this->assertSignal($signalsByKey['days_since_checkin_response'], PatientSignalLevel::RISK, 3, 20, [
            'days_without_response' => 20,
        ]);
        $this->assertSignal($signalsByKey['goal_progress_pace'], PatientSignalLevel::RISK, 3, 0.0, [
            'actual_progress_percent' => 0.0,
            'expected_progress_percent' => 75.0,
            'delta_percent' => -75.0,
        ]);
        $this->assertSignal($signalsByKey['avaliation_frequency'], PatientSignalLevel::GOOD, 0, 6.8, [
            'avg_days_between_avaliations' => 6.8,
        ]);

        $this->assertSame(PatientSignalLevel::RISK, $signalsByKey['unanswered_reminders']['level']);
        $this->assertGreaterThanOrEqual(2, (int) $signalsByKey['unanswered_reminders']['risk_points']);
        $this->assertSame(2, (int) $signalsByKey['unanswered_reminders']['value']);
        $this->assertSame(true, (bool) $signalsByKey['unanswered_reminders']['meta']['has_pending_response']);
        $this->assertGreaterThanOrEqual(15.0, (float) $signalsByKey['unanswered_reminders']['meta']['expiry_ratio']);
        $this->assertSame(2, (int) $signalsByKey['unanswered_reminders']['meta']['unanswered_reminders']);

        $this->assertSame(PatientEvolutionStatus::RISK_OF_ABANDONMENT, $result['summary']['status']);
        $this->assertGreaterThan(67.0, (float) $result['summary']['risk_percent']);
        $this->assertSame(false, (bool) $result['summary']['is_low_confidence']);
        $this->assertGreaterThanOrEqual(60.0, (float) $result['summary']['confidence_percent']);
    }

    public function testEngineCanFilterOutPremiumSignals(): void
    {
        $now = Carbon::create(2026, 5, 28, 12, 0, 0);

        [$user, $client] = $this->createManagerAndClient();
        $this->actingAs($user, 'web');

        $this->seedAvaliations($client, $now);

        DB::table('goals')->insert([
            'client_id' => $client->id,
            'objective' => Goal::OBJECTIVE_WEIGHT_LOSS,
            'initial_weight_kg' => 80.0,
            'target_weight_kg' => 70.0,
            'deadline' => $now->copy()->addDays(10)->format('Y-m-d'),
            'created_at' => $now->copy()->subDays(30),
            'updated_at' => $now->copy()->subDays(30),
        ]);

        $result = app(PatientSignalEngine::class)->evaluateForClient($client->fresh(), false, $now);

        $this->assertCount(3, $result['signals']);

        $freeSignalKeys = array_map(function (array $signal): string {
            return (string) $signal['key'];
        }, $result['signals']);

        $this->assertEqualsCanonicalizing([
            'weight_variability_7d',
            'goal_progress_pace',
            'avaliation_frequency',
        ], $freeSignalKeys);
    }

    public function testEngineAvoidsEvolvingWellWhenConfidenceIsLow(): void
    {
        $now = Carbon::create(2026, 5, 28, 12, 0, 0);

        [$user, $client] = $this->createManagerAndClient();
        $this->actingAs($user, 'web');

        DB::table('goals')->insert([
            'client_id' => $client->id,
            'objective' => Goal::OBJECTIVE_WEIGHT_LOSS,
            'initial_weight_kg' => 80.0,
            'target_weight_kg' => 70.0,
            'deadline' => $now->copy()->addDays(40)->format('Y-m-d'),
            'created_at' => $now->copy()->subDays(1),
            'updated_at' => $now->copy()->subDays(1),
        ]);

        // Only two evaluations => avaiation_frequency=INFO and goal_progress_pace=INFO (recent goal).
        DB::table('avaliations')->insert([
            [
                'client_id' => $client->id,
                'date' => $now->copy()->subDays(2)->format('Y-m-d'),
                'age' => $client->getAge(),
                'weight_kg' => 80.0,
                'height_cm' => $client->height_cm,
                'calculate_perc_fat_by' => Avaliation::CALCULATE_PERC_FAT_BY_BIOIMPEDANCE,
            ],
            [
                'client_id' => $client->id,
                'date' => $now->copy()->subDays(1)->format('Y-m-d'),
                'age' => $client->getAge(),
                'weight_kg' => 79.9,
                'height_cm' => $client->height_cm,
                'calculate_perc_fat_by' => Avaliation::CALCULATE_PERC_FAT_BY_BIOIMPEDANCE,
            ],
        ]);

        $result = app(PatientSignalEngine::class)->evaluateForClient($client->fresh(), false, $now);

        $this->assertSame(PatientEvolutionStatus::STABLE_ATTENTION, $result['summary']['status']);
        $this->assertSame(true, (bool) $result['summary']['is_low_confidence']);
        $this->assertLessThan(60.0, (float) $result['summary']['confidence_percent']);
    }

    public function testCheckinResponseRateTreatsRecentRespondedCycleAsGoodAdherence(): void
    {
        $now = Carbon::create(2026, 6, 5, 9, 0, 0);

        [$user, $client] = $this->createManagerAndClient();
        $this->actingAs($user, 'web');

        $avaliationId = DB::table('avaliations')->insertGetId([
            'client_id' => $client->id,
            'date' => $now->copy()->format('Y-m-d'),
            'age' => $client->getAge(),
            'weight_kg' => 79.8,
            'height_cm' => $client->height_cm,
            'calculate_perc_fat_by' => Avaliation::CALCULATE_PERC_FAT_BY_BIOIMPEDANCE,
        ]);

        AvaliationCheckinField::create([
            'avaliation_id' => $avaliationId,
            'field_class' => WeightField::class,
            'field_type' => 'weight',
            'field_key' => 'weight_kg',
            'response' => '79.8',
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

        $result = app(PatientSignalEngine::class)->evaluateForClient($client->fresh(), true, $now);
        $signalsByKey = $this->indexSignalsByKey($result['signals']);

        $this->assertSignal($signalsByKey['checkin_response_rate_30d'], PatientSignalLevel::GOOD, 0, 100.0, [
            'rate_percent' => 100.0,
            'responses' => 1,
            'expected' => 1,
            'days_since_last_sent' => 0,
        ]);
        $this->assertStringContainsString('1 de 1', (string) ($signalsByKey['checkin_response_rate_30d']['meta']['indicator_text'] ?? ''));
    }

    public function testWeightVariabilityModerateButGoalAlignedIsClassifiedAsGood(): void
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
            ['daysAgo' => 6, 'weight' => 83.0],
            ['daysAgo' => 3, 'weight' => 81.8],
            ['daysAgo' => 1, 'weight' => 82.2],
        ];

        foreach ($rows as $row) {
            DB::table('avaliations')->insert([
                'client_id' => $client->id,
                'date' => $now->copy()->subDays((int) $row['daysAgo'])->format('Y-m-d'),
                'age' => $client->getAge(),
                'weight_kg' => (float) $row['weight'],
                'height_cm' => $client->height_cm,
                'calculate_perc_fat_by' => Avaliation::CALCULATE_PERC_FAT_BY_BIOIMPEDANCE,
            ]);
        }

        $result = app(PatientSignalEngine::class)->evaluateForClient($client->fresh(), false, $now);
        $signalsByKey = $this->indexSignalsByKey($result['signals']);

        $this->assertSame(PatientSignalLevel::GOOD, (string) $signalsByKey['weight_variability_7d']['level']);
        $this->assertSame(0, (int) $signalsByKey['weight_variability_7d']['risk_points']);
        $this->assertSame(true, (bool) ($signalsByKey['weight_variability_7d']['meta']['goal_aligned'] ?? false));
    }

    public function testGoalProgressPaceIsInformationalForHealthObjective(): void
    {
        $now = Carbon::create(2026, 6, 5, 10, 0, 0);

        [$user, $client] = $this->createManagerAndClient();
        $this->actingAs($user, 'web');

        DB::table('goals')->insert([
            'client_id' => $client->id,
            'objective' => Goal::OBJECTIVE_HEALTH,
            'initial_weight_kg' => 80.0,
            'target_weight_kg' => 75.0,
            'deadline' => $now->copy()->addDays(30)->format('Y-m-d'),
            'created_at' => $now->copy()->subDays(15),
            'updated_at' => $now->copy()->subDays(15),
        ]);

        DB::table('avaliations')->insert([
            [
                'client_id' => $client->id,
                'date' => $now->copy()->subDays(10)->format('Y-m-d'),
                'age' => $client->getAge(),
                'weight_kg' => 80.0,
                'height_cm' => $client->height_cm,
                'calculate_perc_fat_by' => Avaliation::CALCULATE_PERC_FAT_BY_BIOIMPEDANCE,
            ],
            [
                'client_id' => $client->id,
                'date' => $now->copy()->subDays(5)->format('Y-m-d'),
                'age' => $client->getAge(),
                'weight_kg' => 79.7,
                'height_cm' => $client->height_cm,
                'calculate_perc_fat_by' => Avaliation::CALCULATE_PERC_FAT_BY_BIOIMPEDANCE,
            ],
            [
                'client_id' => $client->id,
                'date' => $now->copy()->subDays(1)->format('Y-m-d'),
                'age' => $client->getAge(),
                'weight_kg' => 79.6,
                'height_cm' => $client->height_cm,
                'calculate_perc_fat_by' => Avaliation::CALCULATE_PERC_FAT_BY_BIOIMPEDANCE,
            ],
        ]);

        $result = app(PatientSignalEngine::class)->evaluateForClient($client->fresh(), false, $now);
        $signalsByKey = $this->indexSignalsByKey($result['signals']);

        $this->assertSame(PatientSignalLevel::INFO, (string) $signalsByKey['goal_progress_pace']['level']);
        $this->assertSame(0, (int) $signalsByKey['goal_progress_pace']['risk_points']);
        $this->assertSame(true, (bool) ($signalsByKey['goal_progress_pace']['meta']['objective_health'] ?? false));
    }

    private function seedAvaliations(Client $client, Carbon $now): void
    {
        $rows = [
            ['daysAgo' => 28, 'weight' => 80.0],
            ['daysAgo' => 14, 'weight' => 81.0],
            ['daysAgo' => 6, 'weight' => 82.4],
            ['daysAgo' => 3, 'weight' => 81.9],
            ['daysAgo' => 1, 'weight' => 83.1],
        ];

        foreach ($rows as $index => $row) {
            $avaliationId = DB::table('avaliations')->insertGetId([
                'client_id' => $client->id,
                'date' => $now->copy()->subDays((int) $row['daysAgo'])->format('Y-m-d'),
                'age' => $client->getAge(),
                'weight_kg' => (float) $row['weight'],
                'height_cm' => $client->height_cm,
                'calculate_perc_fat_by' => Avaliation::CALCULATE_PERC_FAT_BY_BIOIMPEDANCE,
            ]);

            if ($index === 4) {
                AvaliationCheckinField::create([
                    'avaliation_id' => $avaliationId,
                    'field_class' => WeightField::class,
                    'field_type' => 'weight',
                    'field_key' => 'weight_kg',
                    'response' => (string) $row['weight'],
                    'response_type' => AvaliationCheckinField::RESPONSE_TYPE_NUMBER,
                    'field_meta' => [],
                ]);
            }
        }
    }

    /**
     * @param array<int, array<string, mixed>> $signals
     * @return array<string, array<string, mixed>>
     */
    private function indexSignalsByKey(array $signals): array
    {
        $indexed = [];

        foreach ($signals as $signal) {
            $indexed[(string) $signal['key']] = $signal;
        }

        return $indexed;
    }

    /**
     * @param array<string, mixed> $signal
     * @param array<string, mixed> $expectedMetaSubset
     */
    private function assertSignal(array $signal, string $expectedLevel, int $expectedRiskPoints, $expectedValue, array $expectedMetaSubset): void
    {
        $this->assertSame($expectedLevel, (string) $signal['level']);
        $this->assertSame($expectedRiskPoints, (int) $signal['risk_points']);
        $this->assertEquals($expectedValue, $signal['value']);

        foreach ($expectedMetaSubset as $key => $expectedMetaValue) {
            $this->assertArrayHasKey($key, $signal['meta']);
            $this->assertEquals($expectedMetaValue, $signal['meta'][$key]);
        }
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
