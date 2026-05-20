<?php

namespace Tests\Feature\Helpers;

use Tests\TestCase;
use App\Helpers\ValidatePassword;
use App\Helpers\ApiResponse;

class ValidatePasswordTest extends TestCase
{
    private function getHasNumberMessage(): string
    {
        return __('messages.components.ValidatePassword.hasNumber');
    }

    private function getMinCharMessage(): string
    {
        return __('messages.components.ValidatePassword.minChar');
    }

    public function testValidPassword()
    {
        $validator = new ValidatePassword('password1');
        $response = $validator->validate();

        $this->assertInstanceOf(ApiResponse::class, $response);
        $this->assertFalse($response->isError());
        $this->assertEquals(__('messages.components.ValidatePassword.validHtml'), $response->getMessage());
        $this->assertEquals([], $response->getValueFromResponse('ret'));
    }

    public function testPasswordTooShort()
    {
        $validator = new ValidatePassword('pass1');
        $response = $validator->validate();

        $this->assertTrue($response->isError());
        $this->assertStringContainsString($this->getMinCharMessage(), $response->getMessage());
        $this->assertContains($this->getMinCharMessage(), $response->getValueFromResponse('ret'));
    }

    public function testPasswordMissingNumber()
    {
        $validator = new ValidatePassword('password');
        $response = $validator->validate();

        $this->assertTrue($response->isError());
        $this->assertStringContainsString($this->getHasNumberMessage(), $response->getMessage());
        $this->assertContains($this->getHasNumberMessage(), $response->getValueFromResponse('ret'));
    }

    public function testPasswordTooShortAndMissingNumber()
    {
        $validator = new ValidatePassword('pass');
        $response = $validator->validate();

        $this->assertTrue($response->isError());
        $this->assertStringContainsString($this->getMinCharMessage(), $response->getMessage());
        $this->assertStringContainsString($this->getHasNumberMessage(), $response->getMessage());
        $this->assertContains($this->getMinCharMessage(), $response->getValueFromResponse('ret'));
        $this->assertContains($this->getHasNumberMessage(), $response->getValueFromResponse('ret'));
    }

    public function testGetRulesTexts()
    {
        $rules = ValidatePassword::getRulesTexts();
        $this->assertContains($this->getMinCharMessage(), $rules);
        $this->assertContains($this->getHasNumberMessage(), $rules);
    }
}
