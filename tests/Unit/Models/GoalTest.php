<?php

namespace Tests\Unit\Models;

use App\Models\Goal;
use Tests\TestCase;

class GoalTest extends TestCase
{
    public function testObjectiveWeightLossUsesExplicitObjectiveConstant(): void
    {
        $goal = new Goal();
        $goal->objective = Goal::OBJECTIVE_WEIGHT_LOSS;
        $goal->initial_weight_kg = 70;
        $goal->target_weight_kg = 75;

        $this->assertTrue($goal->isObjectiveWeightLoss());
        $this->assertFalse($goal->isObjectiveMuscleGain());
    }

    public function testObjectiveMuscleGainUsesExplicitObjectiveConstant(): void
    {
        $goal = new Goal();
        $goal->objective = Goal::OBJECTIVE_MUSCLE_GAIN;
        $goal->initial_weight_kg = 80;
        $goal->target_weight_kg = 75;

        $this->assertTrue($goal->isObjectiveMuscleGain());
        $this->assertFalse($goal->isObjectiveWeightLoss());
    }

    public function testObjectiveHealthDoesNotForceWeightLossOrGainDirection(): void
    {
        $goal = new Goal();
        $goal->objective = Goal::OBJECTIVE_HEALTH;
        $goal->initial_weight_kg = 90;
        $goal->target_weight_kg = 80;

        $this->assertFalse($goal->isObjectiveWeightLoss());
        $this->assertFalse($goal->isObjectiveMuscleGain());
    }

    public function testLegacyFallbackStillUsesWeightComparisonWhenObjectiveIsMissing(): void
    {
        $loss = new Goal();
        $loss->objective = null;
        $loss->initial_weight_kg = 90;
        $loss->target_weight_kg = 80;

        $gain = new Goal();
        $gain->objective = null;
        $gain->initial_weight_kg = 70;
        $gain->target_weight_kg = 75;

        $this->assertTrue($loss->isObjectiveWeightLoss());
        $this->assertFalse($loss->isObjectiveMuscleGain());
        $this->assertFalse($gain->isObjectiveWeightLoss());
        $this->assertTrue($gain->isObjectiveMuscleGain());
    }
}
