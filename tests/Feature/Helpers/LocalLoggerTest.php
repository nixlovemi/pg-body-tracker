<?php

namespace Tests\Feature\Helpers;

use Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Route;
use App\Helpers\LocalLogger;
use App\Models\User;

class LocalLoggerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        // Fake the storage for log writing
        Storage::fake('local');
    }

    public function testLogWritesToFile()
    {
        // Mock SysUtils static methods
        $date = now();
        $dateString = $date->format('D');

        // Mock Route
        Route::shouldReceive('currentRouteName')->andReturn('test.route');

        // Simulate a logged-in user
        $User = User::where('role', User::ROLE_MANAGER)->first();
        $this->be($User);

        // Set request method and request array
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_REQUEST = ['foo' => 'bar'];

        // Call logger
        LocalLogger::log('Feature test log', ['extra' => 'value']);

        // Assert log file exists and contains expected content
        $logFile = 'logs/' . $dateString . '.log';

        $contents = Storage::disk('local')->get($logFile);
        $this->assertStringContainsString('Feature test log', $contents);
        $this->assertStringContainsString('JSON:', $contents);
        $this->assertStringContainsString('"extra":"value"', $contents);
        $this->assertStringContainsString('"route":"test.route"', $contents);
        $this->assertStringContainsString('"reqMethod":"POST"', $contents);
        $this->assertStringContainsString('"foo":"bar"', $contents);
        $this->assertStringContainsString('"userId":' . $User->id, $contents);
    }

    public function testGetLogVarsReturnsExpectedKeys()
    {
        // Mock Route
        Route::shouldReceive('currentRouteName')->andReturn('feature.route');

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_REQUEST = ['bar' => 'baz'];

        $vars = LocalLogger::getLogVars();

        $this->assertArrayHasKey('userId', $vars);
        $this->assertArrayHasKey('reqMethod', $vars);
        $this->assertEquals('GET', $vars['reqMethod']);
        $this->assertArrayHasKey('route', $vars);
        $this->assertEquals('feature.route', $vars['route']);
        $this->assertArrayHasKey('request', $vars);
        $this->assertEquals(['bar' => 'baz'], $vars['request']);
        $this->assertArrayHasKey('backTrace', $vars);
        $this->assertIsArray($vars['backTrace']);
    }
}
