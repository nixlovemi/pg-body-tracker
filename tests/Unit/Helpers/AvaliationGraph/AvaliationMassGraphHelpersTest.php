<?php

namespace Tests\Unit\Helpers\AvaliationGraph;

use App\Helpers\AvaliationGraph\AvaliationFatMassGraphHelper;
use App\Helpers\AvaliationGraph\AvaliationGraphAbstract;
use App\Helpers\AvaliationGraph\AvaliationLeanMassGraphHelper;
use App\Helpers\AvaliationGraph\AvaliationWaistAbdomenCircumferenceGraphHelper;
use App\Helpers\AvaliationGraph\AvaliationTrunkFatPercentageGraphHelper;
use App\Helpers\AvaliationGraph\AvaliationSkeletalMuscleGraphHelper;
use ReflectionClass;
use Tests\TestCase;

class AvaliationMassGraphHelpersTest extends TestCase
{
    public function testFatMassGraphHelperIsConcreteGraphHelper(): void
    {
        $reflection = new ReflectionClass(AvaliationFatMassGraphHelper::class);

        $this->assertTrue($reflection->isFinal());
        $this->assertTrue($reflection->isSubclassOf(AvaliationGraphAbstract::class));
    }

    public function testLeanMassGraphHelperIsConcreteGraphHelper(): void
    {
        $reflection = new ReflectionClass(AvaliationLeanMassGraphHelper::class);

        $this->assertTrue($reflection->isFinal());
        $this->assertTrue($reflection->isSubclassOf(AvaliationGraphAbstract::class));
    }

    public function testGraphHelpersExposeExpectedClassNames(): void
    {
        $fatHelper = (new ReflectionClass(AvaliationFatMassGraphHelper::class))->newInstanceWithoutConstructor();
        $leanHelper = (new ReflectionClass(AvaliationLeanMassGraphHelper::class))->newInstanceWithoutConstructor();
        $waistAbdomenHelper = (new ReflectionClass(AvaliationWaistAbdomenCircumferenceGraphHelper::class))->newInstanceWithoutConstructor();
        $trunkFatHelper = (new ReflectionClass(AvaliationTrunkFatPercentageGraphHelper::class))->newInstanceWithoutConstructor();
        $skeletalMuscleHelper = (new ReflectionClass(AvaliationSkeletalMuscleGraphHelper::class))->newInstanceWithoutConstructor();

        $this->assertSame('AvaliationFatMassGraph', $fatHelper->getClassName());
        $this->assertSame('AvaliationLeanMassGraph', $leanHelper->getClassName());
        $this->assertSame('AvaliationWaistAbdomenCircumferenceGraph', $waistAbdomenHelper->getClassName());
        $this->assertSame('AvaliationTrunkFatPercentageGraph', $trunkFatHelper->getClassName());
        $this->assertSame('AvaliationSkeletalMuscleGraph', $skeletalMuscleHelper->getClassName());
    }

    public function testWaistAbdomenGraphHelperIsConcreteGraphHelper(): void
    {
        $reflection = new ReflectionClass(AvaliationWaistAbdomenCircumferenceGraphHelper::class);

        $this->assertTrue($reflection->isFinal());
        $this->assertTrue($reflection->isSubclassOf(AvaliationGraphAbstract::class));
    }

    public function testTrunkFatPercentageGraphHelperIsConcreteGraphHelper(): void
    {
        $reflection = new ReflectionClass(AvaliationTrunkFatPercentageGraphHelper::class);

        $this->assertTrue($reflection->isFinal());
        $this->assertTrue($reflection->isSubclassOf(AvaliationGraphAbstract::class));
    }

    public function testSkeletalMuscleGraphHelperIsConcreteGraphHelper(): void
    {
        $reflection = new ReflectionClass(AvaliationSkeletalMuscleGraphHelper::class);

        $this->assertTrue($reflection->isFinal());
        $this->assertTrue($reflection->isSubclassOf(AvaliationGraphAbstract::class));
    }
}
