<?php

namespace Tests\Unit\Helpers;

use PHPUnit\Framework\TestCase;
use App\Helpers\Constants;

class ConstantsTest extends TestCase
{
    public function testUserDefaultImagePath()
    {
        $this->assertEquals('/images/no-user.jpg', Constants::USER_DEFAULT_IMAGE_PATH);
    }

    public function testUserLogoDefaultImagePath()
    {
        $this->assertEquals('/images/no-logo.png', Constants::USER_LOGO_DEFAULT_IMAGE_PATH);
    }

    public function testRegexPhoneNumber()
    {
        $this->assertEquals('/(?=.*[0-9])[- +()0-9]+/', Constants::REGEX_PHONE_NUMBER);
    }

    public function testWhatsLinkUrl()
    {
        $this->assertEquals('https://api.whatsapp.com/send?phone=%s&text=%s', Constants::WHATS_LINK_URL);
    }

    public function testFormActions()
    {
        $this->assertContains(Constants::FORM_ADD, Constants::FORM_ACTIONS);
        $this->assertContains(Constants::FORM_EDIT, Constants::FORM_ACTIONS);
        $this->assertContains(Constants::FORM_VIEW, Constants::FORM_ACTIONS);
    }

    public function testReturnIntCantCalculate()
    {
        $this->assertEquals(-999, Constants::RETURN_INT_CANT_CALCULATE);
    }

    public function testGraphColors()
    {
        $this->assertContains('rgba(255, 179, 186, 0.7)', [
            Constants::GRAPH_COLOR_LIGHT_PINK,
            Constants::GRAPH_COLOR_PEACH,
            Constants::GRAPH_COLOR_YELLOW,
            Constants::GRAPH_COLOR_MINT,
            Constants::GRAPH_COLOR_LIGHT_BLUE,
            Constants::GRAPH_COLOR_LILAC,
            Constants::GRAPH_COLOR_LAVENDER,
            Constants::GRAPH_COLOR_LIGHT_GREEN,
        ]);
    }

    public function testRankColors()
    {
        $this->assertCount(8, Constants::RANK_COLORS);
        $this->assertEquals('#8BC34A', Constants::RANK_COLORS[0]);
    }

    public function testRankColorsKeysExists()
    {
        for ($i = 1; $i <= 8; $i++) {
            $this->assertTrue(defined("App\Helpers\Constants::RANK_COLOR_$i"));
        }

        $this->assertTrue(defined('App\Helpers\Constants::RANK_COLOR_DEFAULT'));
    }

    public function testGraphColorKeysExists()
    {
        $this->assertTrue(defined('App\Helpers\Constants::GRAPH_COLOR_LIGHT_PINK'));
        $this->assertTrue(defined('App\Helpers\Constants::GRAPH_COLOR_PEACH'));
        $this->assertTrue(defined('App\Helpers\Constants::GRAPH_COLOR_YELLOW'));
        $this->assertTrue(defined('App\Helpers\Constants::GRAPH_COLOR_MINT'));
        $this->assertTrue(defined('App\Helpers\Constants::GRAPH_COLOR_LIGHT_BLUE'));
        $this->assertTrue(defined('App\Helpers\Constants::GRAPH_COLOR_LILAC'));
        $this->assertTrue(defined('App\Helpers\Constants::GRAPH_COLOR_LAVENDER'));
        $this->assertTrue(defined('App\Helpers\Constants::GRAPH_COLOR_LIGHT_GREEN'));
    }
}
