<?php

namespace Tests\Feature\Helpers;

use Tests\TestCase;
use App\Helpers\Constants;

class ConstantsTest extends TestCase
{
    public function testGetRankBarInfoReturnsArray()
    {
        $result = Constants::getRankBarInfo();
        $this->assertIsArray($result);
    }

    public function testGetRankingsReturnsAllRanks()
    {
        $result = Constants::getRankings();
        $this->assertCount(8, $result);
        foreach ($result as $rank) {
            $this->assertArrayHasKey('color', $rank);
            $this->assertArrayHasKey('rank', $rank);
            $this->assertArrayHasKey('label', $rank);
            $this->assertArrayHasKey('labelMin', $rank);
        }
    }

    public function testGetRankingsWithShowOnlyIndexes()
    {
        $result = Constants::getRankings([0, 2]);
        $this->assertCount(2, $result);
        $this->assertEquals(1, $result[0]['rank']);
        $this->assertEquals(3, $result[1]['rank']);
    }

    public function testGetRankingsWithRemoveNbrFromLabel()
    {
        // Mock __() helper to return a string with numbers for testing
        if (!function_exists('__')) {
            function __($key) {
                return "Label123";
            }
        }
        $result = Constants::getRankings([], true);
        foreach ($result as $rank) {
            $this->assertDoesNotMatchRegularExpression('/\d+/', $rank['label']);
            $this->assertDoesNotMatchRegularExpression('/\d+/', $rank['labelMin']);
        }
    }
}
