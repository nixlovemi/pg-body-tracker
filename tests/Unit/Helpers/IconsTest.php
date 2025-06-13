<?php

namespace Tests\Unit\Helpers;

use PHPUnit\Framework\TestCase;
use App\Helpers\Icons;

class IconsTest extends TestCase
{
    public function testAllIconsConstants()
    {
        $this->assertEquals('<i class="fas fa-info-circle"></i>', Icons::INFO_CIRCLE);
        $this->assertEquals('<i class="fab fa-google fa-fw"></i>', Icons::GOOGLE);
        $this->assertEquals('<i class="fas fa-plus"></i>', Icons::PLUS);
        $this->assertEquals('<i class="fas fa-weight"></i>', Icons::WEIGHT);
        $this->assertEquals('<i class="fa fa-bars"></i>', Icons::BARS);
        $this->assertEquals('<i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>', Icons::SIGN_OUT_GREY);
        $this->assertEquals('<i class="fas fa-angle-up"></i>', Icons::ANGLE_UP);
        $this->assertEquals('<i class="fa-solid fas fa-eye fa-fw"></i>', Icons::EYE);
        $this->assertEquals('<i class="fa-solid fas fa-pencil-alt fa-fw"></i>', Icons::EDIT);
        $this->assertEquals('<i class="fas fa-chart-line fa-fw"></i>', Icons::FILE_CHART);
        $this->assertEquals('<i class="fas fa-file-alt fa-fw"></i>', Icons::FILE_REPORT);
        $this->assertEquals('<i class="fas fa-arrow-up"></i>', Icons::ARROW_UP);
        $this->assertEquals('<i class="fas fa-arrow-down"></i>', Icons::ARROW_DOWN);
        $this->assertEquals('<i class="fas fa-user fa-fw mr-2 text-gray-400"></i>', Icons::USER_GREY);
        $this->assertEquals('<i class="fas fa-key fa-fw mr-2 text-gray-400"></i>', Icons::KEY_GREY);
        $this->assertEquals('<i class="fas fa-home"></i>', Icons::HOME);
        $this->assertEquals('<i class="fas fa-envelope"></i>', Icons::ENVELOP);
        $this->assertEquals('<i class="fab fa-whatsapp-square"></i>', Icons::WHATSAPP);
        $this->assertEquals('<i class="fas fa-fw fa-tachometer-alt"></i>', Icons::TACHOMETER);
        $this->assertEquals('<i class="fas fa-fw fa-users"></i>', Icons::USERS);
        $this->assertEquals('<i class="fas fa-user-clock"></i>', Icons::USER_CLOCK);
        $this->assertEquals('<i class="fas fa-bullseye"></i>', Icons::BULLS_EYE);
        $this->assertEquals('<i class="fas fa-user-plus"></i>', Icons::USER_PLUS);
        $this->assertEquals('<i class="far fa-calendar-alt"></i>', Icons::CALENDAR_ALT);
        $this->assertEquals('<i class="fas fa-birthday-cake"></i>', Icons::BIRTHDAY_CAKE);
        $this->assertEquals('<i class="fas fa-chart-bar"></i>', Icons::CHART_BAR);
        $this->assertEquals('<i class="fas fa-calendar-times"></i>', Icons::CALENDAR_TIME);
        $this->assertEquals('<i class="fas fa-download"></i>', Icons::DOWNLOAD);
        $this->assertEquals('<i class="fas fa-file-pdf"></i>', Icons::FILE_PDF);
        $this->assertEquals('<i class="fas fa-file-csv"></i>', Icons::FILE_CSV);
        $this->assertEquals('<i class="fas fa-hourglass-half"></i>', Icons::HOURGLASS_HALF);
        $this->assertEquals('<i class="fas fa-user-friends"></i>', Icons::USER_FRIENDS);
        $this->assertEquals('<i class="fas fa-exchange-alt"></i>', Icons::EXCHANGE_ALT);
        $this->assertEquals('<i class="fas fa-trophy"></i>', Icons::TROPHY);
        $this->assertEquals('<i class="fas fa-question-circle"></i>', Icons::QUESTION_CIRCLE);
        $this->assertEquals('<i class="fas fa-check"></i>', Icons::CHECK);
        $this->assertEquals('<i class="fas fa-times"></i>', Icons::TIMES);
        $this->assertEquals('<i class="fas fa-file-invoice-dollar"></i>', Icons::FILE_INVOICE_DOLAR);
        $this->assertEquals('<i class="fas fa-headset"></i>', Icons::HEADSET);
    }

    public function testNumberOfConstants()
    {
        $reflection = new \ReflectionClass(Icons::class);
        $constants = $reflection->getConstants();
        $this->assertCount(39, $constants, 'There should be 39 icon constants defined in the Icons class.');
    }
}
