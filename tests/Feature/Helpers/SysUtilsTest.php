<?php

namespace Tests\Feature\Helpers;

use Tests\TestCase;
use App\Helpers\SysUtils;
use App\Models\User;
use App\Http\Middleware\AuthenticateWebExpireAt;
use Carbon\Carbon;

class SysUtilsTest extends TestCase
{
    public function testIsRunningMigration()
    {
        $_SERVER['argv'] = ['artisan', 'migrate'];
        $this->assertTrue(SysUtils::isRunningMigration());

        $_SERVER['argv'] = ['artisan', 'serve'];
        $this->assertFalse(SysUtils::isRunningMigration());
    }

    public function testEncodeAndDecodeStr()
    {
        $original = 'Test123';
        $encoded = SysUtils::encodeStr($original);
        $decoded = SysUtils::decodeStr($encoded);
        $expected = preg_replace('/[^\p{L}\p{N}]/u', '@', $original);
        $this->assertEquals($expected, $decoded);
    }

    public function testSanitizeFileNameForUpload()
    {
        $fileName = 'Tést@File 2024!.jpg';
        $sanitized = SysUtils::sanitizeFileNameForUpload($fileName);
        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9\.\-_]+$/', $sanitized);
    }

    public function testGetArrayOnlyKeys()
    {
        $array = ['a' => 1, 'b' => 2, 'c' => 3];
        $result = SysUtils::getArrayOnlyKeys($array, ['a', 'c']);
        $this->assertEquals(['a' => 1, 'c' => 3], $result);
    }

    public function testFormatNumberToDb()
    {
        $number = 'R$ 1.234,56';
        $result = SysUtils::formatNumberToDb($number, 2, ',', '.');
        $this->assertEquals(1234.56, $result);
    }

    public function testFormatDbToNumber()
    {
        // Mock translation
        app()->setLocale('pt_BR');
        $result = SysUtils::formatDbToNumber('1234.56', 2);
        $this->assertIsString($result);
    }

    public function testFormatCurrencyBr()
    {
        $result = SysUtils::formatCurrencyBr(1234.56, 2, 'R$');
        $this->assertEquals('R$ 1.234,56', $result);
    }

    public function testReformatDate()
    {
        $date = '2024-06-15';
        $result = SysUtils::reformatDate($date, 'Y-m-d', 'd/m/Y');
        $this->assertEquals('15/06/2024', $result);
    }

    public function testHexToRGB()
    {
        $rgb = SysUtils::hexToRGB('#ff00aa');
        $this->assertEquals([255, 0, 170], $rgb);

        $rgbShort = SysUtils::hexToRGB('#f0a');
        $this->assertEquals([255, 0, 170], $rgbShort);

        $rgbInvalid = SysUtils::hexToRGB('#zzzzzz');
        $this->assertEquals([0, 0, 0], $rgbInvalid);

        $rgbFromConstants = SysUtils::hexToRGB('#8BC34A');
        $this->assertEquals([139, 195, 74], $rgbFromConstants);
    }

    public function testGetBase64String()
    {
        $file = tempnam(sys_get_temp_dir(), 'test');
        file_put_contents($file, 'test');
        $result = SysUtils::getBase64String($file);
        $this->assertStringStartsWith('data:', $result);
        unlink($file);
    }

    public function testGetOsPhotosFolder()
    {
        $result = SysUtils::getOsPhotosFolder('/photos/image.jpg', 'admin');
        $this->assertStringContainsString('admin', $result);
        $this->assertStringContainsString(DIRECTORY_SEPARATOR, $result);
    }

    public function testGetFormattedDeltaText()
    {
        // Define dummy Icons if not present
        if (!defined('\App\Helpers\Icons::ARROW_UP')) {
            define('\App\Helpers\Icons::ARROW_UP', '↑');
            define('\App\Helpers\Icons::ARROW_DOWN', '↓');
        }
        $result = SysUtils::getFormattedDeltaText(5.5, 'kg');
        $this->assertStringContainsString('+', $result);
        $this->assertStringContainsString('kg', $result);

        $resultNeg = SysUtils::getFormattedDeltaText(-2.3, 'cm');
        $this->assertStringContainsString('-', $resultNeg);
        $this->assertStringContainsString('cm', $resultNeg);
    }

    public function testApplyTimezone()
    {
        putenv('APP_TIME_ZONE=America/Sao_Paulo');
        $date = '2024-06-15 12:00:00';
        $carbon = SysUtils::applyTimezone($date);
        $this->assertInstanceOf(Carbon::class, $carbon);
        $this->assertEquals('America/Sao_Paulo', $carbon->getTimezone()->getName());
    }

    public function testTimezoneDate()
    {
        putenv('APP_TIME_ZONE=America/Sao_Paulo');
        $date = '2024-06-15 12:00:00';
        $formatted = SysUtils::timezoneDate($date, 'd/m/Y H:i');
        $this->assertEquals('15/06/2024 12:00', $formatted);
    }

    public function testTimezoneNow()
    {
        putenv('APP_TIME_ZONE=America/Sao_Paulo');
        $now = SysUtils::timezoneNow('Y');
        $this->assertEquals(date('Y'), $now);
    }

    public function testIsLoggedInAndGetLoggedInUser()
    {
        $user = User::where('active', 1)->inRandomOrder()->first();
        SysUtils::loginUser($user);

        $this->assertTrue(SysUtils::isLoggedIn());
        $this->assertInstanceOf(User::class, SysUtils::getLoggedInUser());
        $this->assertEquals($user->id, SysUtils::getLoggedInUser()->id);
    }

    public function testLoginUserAndLogout()
    {
        $user = User::factory()->create();
        $this->assertTrue(SysUtils::loginUser($user));
        $this->assertTrue(SysUtils::isLoggedIn());

        SysUtils::logout();
        $this->assertFalse(SysUtils::isLoggedIn());
    }

    public function testLoginUserTempById()
    {
        $user = User::factory()->create();
        $this->assertTrue(SysUtils::loginUserTempById($user->id, 10));
        $this->assertTrue(session()->has(AuthenticateWebExpireAt::SESSION_NAME));
    }

    public function testGetWebAuth()
    {
        $guard = SysUtils::getWebAuth();
        $this->assertInstanceOf(\Illuminate\Contracts\Auth\Guard::class, $guard);
    }

    public function testGetImageBase64ReturnsNullForMissingFile()
    {
        $this->assertNull(SysUtils::getImageBase64('/path/to/nonexistent/file.jpg'));
    }
}
