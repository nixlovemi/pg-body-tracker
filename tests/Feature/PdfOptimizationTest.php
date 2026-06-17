<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Avaliation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PdfOptimizationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that PDF optimization methods work correctly
     */
    public function test_pdf_optimization_batch_loading()
    {
        // Authenticate a user to avoid permission errors during seeding
        /** @var User $user */
        $user = User::factory()->create(['role' => User::ROLE_MANAGER]);
        $this->actingAs($user, 'web');

        // Seed the database
        $this->seed();

        // Get an avaliation with previous records
        $avaliation = Avaliation::with('client')->firstWhere('client_id', '>', 0);

        $this->assertNotNull($avaliation, 'No avaliation found in seeded database');

        // Test 1: getPreviousAvaliationsForReports works
        $previousAvaliations = $avaliation->getPreviousAvaliationsForReports(9);
        $this->assertIsObject($previousAvaliations);
        $this->assertLessThanOrEqual(9, $previousAvaliations->count());

        // Test 2: Graph helper accepts pre-loaded avaliations
        $helperClass = 'App\Helpers\AvaliationGraph\AvaliationBodyCompositionGraphHelper';
        $helper = new $helperClass($avaliation->id, true);

        // Should have setPreviousAvaliations method
        $this->assertTrue(method_exists($helper, 'setPreviousAvaliations'));

        // Set previous avaliations
        $helper->setPreviousAvaliations($previousAvaliations);

        // getData should not fail (it internally calls getPreviousAvaliations)
        $data = $helper->getData();
        $this->assertIsArray($data);
        $this->assertArrayHasKey('config', $data);

        // Test 3: ChartPhp cache key uses config hash (not element ID)
        $componentClass = 'App\View\Components\ChartPhp';
        $config = json_encode(['type' => 'line', 'data' => []]);
        $component = new $componentClass('test-element-id', $config);

        // Cache key should be based on config hash, not element ID
        // We can't directly test the private method, but we can verify
        // that the component renders without errors
        $view = $component->render();
        $this->assertNotNull($view);

        // Test 4: AvaliationReport component accepts pre-loaded data
        $reportComponent = new \App\View\Components\AvaliationReport(
            $avaliation->id,
            true,
            $previousAvaliations,
            null
        );
        $this->assertNotNull($reportComponent->Avaliation);
        $this->assertEquals($previousAvaliations, $reportComponent->previousAvaliations);
    }

    /**
     * Test that PDF generation doesn't fail with optimizations
     */
    public function test_pdf_generation_with_optimizations()
    {
        // Authenticate a user to avoid permission errors during seeding
        /** @var User $user */
        $user = User::factory()->create(['role' => User::ROLE_MANAGER]);
        $this->actingAs($user, 'web');

        $this->seed();

        $avaliation = Avaliation::with('client')->firstWhere('client_id', '>', 0);
        $this->assertNotNull($avaliation);

        // Test the controller method indirectly by checking that
        // the PDF view can be loaded with all optimization parameters
        $previousAvaliations = $avaliation->getPreviousAvaliationsForReports(9);
        $infoCardsData = \App\Presenters\AvaliationReportPresenter::getInfoCardsData($avaliation);

        // Both should be non-empty
        $this->assertIsArray($infoCardsData);
        $this->assertGreaterThan(0, count($infoCardsData));

        // Verify card data structure
        foreach ($infoCardsData as $card) {
            $this->assertArrayHasKey('method', $card);
            $this->assertArrayHasKey('title', $card);
            $this->assertArrayHasKey('info', $card);
        }
    }
}
