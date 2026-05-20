<?php

namespace Tests\Feature\Helpers;

use Tests\TestCase;
use App\Helpers\ModelValidation;
use App\Helpers\Country;
use App\Helpers\ApiResponse;

class ModelValidationTest extends TestCase
{
    public function testValidateWithValidFields()
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+55 (11) 91234-5678',
            'country' => Country::getCountries()[0],
        ];

        $validation = new ModelValidation($data);
        $validation->addField('name', ['required', 'string', 'min:2'], 'Name');
        $validation->addEmailField('email', 'E-mail');
        $validation->addPhoneField('phone', 'Telefone');
        $validation->addCountryField('country', 'País');

        $response = $validation->validate();

        $this->assertInstanceOf(ApiResponse::class, $response);
        $this->assertFalse($response->isError());
    }

    public function testValidateWithAddIdField()
    {
        $data = [
            'user_id' => 1,
        ];

        $validation = new ModelValidation($data);
        $validation->addIdField(\App\Models\User::class, 'User', 'user_id', 'User ID');

        $response = $validation->validate();

        $this->assertInstanceOf(ApiResponse::class, $response);
        $this->assertFalse($response->isError());
    }

    public function testValidateWithInvalidIdField()
    {
        $data = [
            'user_id' => -1, // Invalid ID
        ];

        $validation = new ModelValidation($data);
        $validation->addIdField(\App\Models\User::class, 'User', 'user_id', 'User ID');

        $response = $validation->validate();

        $this->assertInstanceOf(ApiResponse::class, $response);
        $this->assertTrue($response->isError());
    }

    public function testValidateWithInvalidEmail()
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'not-an-email',
            'phone' => '+55 (11) 91234-5678',
            'country' => Country::getCountries()[0],
        ];

        $validation = new ModelValidation($data);
        $validation->addField('name', ['required', 'string', 'min:2'], 'Name');
        $validation->addEmailField('email', 'E-mail');
        $validation->addPhoneField('phone', 'Telefone');
        $validation->addCountryField('country', 'País');

        $response = $validation->validate();

        $this->assertInstanceOf(ApiResponse::class, $response);
        $this->assertTrue($response->isError());
        $this->assertStringContainsString(__('messages.helpers.modelValidation.verifyBeforeSave'), $response->getMessage());
    }

    public function testValidateWithInvalidPhone()
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => 'invalid-phone',
            'country' => Country::getCountries()[0],
        ];

        $validation = new ModelValidation($data);
        $validation->addField('name', ['required', 'string', 'min:2'], 'Name');
        $validation->addEmailField('email', 'E-mail');
        $validation->addPhoneField('phone', 'Telefone');
        $validation->addCountryField('country', 'País');

        $response = $validation->validate();

        $this->assertInstanceOf(ApiResponse::class, $response);
        $this->assertTrue($response->isError());
    }

    public function testValidateWithInvalidCountry()
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '+55 (11) 91234-5678',
            'country' => 'Neverland',
        ];

        $validation = new ModelValidation($data);
        $validation->addField('name', ['required', 'string', 'min:2'], 'Name');
        $validation->addEmailField('email', 'E-mail');
        $validation->addPhoneField('phone', 'Telefone');
        $validation->addCountryField('country', 'País');

        $response = $validation->validate();

        $this->assertInstanceOf(ApiResponse::class, $response);
        $this->assertTrue($response->isError());
    }

    public function testValidateWithProvinceField()
    {
        $country = Country::getCountries()[0];
        $provinces = Country::getProvinceByCountry($country);
        $provinceKey = array_key_first($provinces);

        $data = [
            'province' => $provinceKey,
        ];

        $validation = new ModelValidation($data);
        $validation->addProvinceField('province', 'Estado');

        $response = $validation->validate();

        $this->assertInstanceOf(ApiResponse::class, $response);
        $this->assertFalse($response->isError());
    }

    public function testValidateWithInvalidProvinceField()
    {
        $data = [
            'province' => 'ZZ',
        ];

        $validation = new ModelValidation($data);
        $validation->addProvinceField('province', 'Estado');

        $response = $validation->validate();

        $this->assertInstanceOf(ApiResponse::class, $response);
        $this->assertTrue($response->isError());
    }

    public function testValidateWithEmptyFields()
    {
        $data = [];

        $validation = new ModelValidation($data);
        $validation->addField('name', ['required', 'string', 'min:2'], 'Name');
        $validation->addEmailField('email', 'E-mail');
        $validation->addPhoneField('phone', 'Telefone');
        $validation->addCountryField('country', 'País');

        $response = $validation->validate();

        $this->assertInstanceOf(ApiResponse::class, $response);
        $this->assertTrue($response->isError());
    }

    public function testMultipleInvalidFields()
    {
        $data = [
            'name' => '',
            'email' => 'not-an-email',
            'phone' => 'invalid-phone',
            'country' => 'Neverland',
        ];

        $validation = new ModelValidation($data);
        $validation->addField('name', ['required', 'string', 'min:2'], 'Name');
        $validation->addEmailField('email', 'E-mail');
        $validation->addPhoneField('phone', 'Telefone');
        $validation->addCountryField('country', 'País');

        $response = $validation->validate();

        $this->assertInstanceOf(ApiResponse::class, $response);
        $this->assertTrue($response->isError());
        $this->assertStringContainsString(__('messages.helpers.modelValidation.verifyBeforeSave'), $response->getMessage());

        $arrResponse = $response->getArrayResponse();
        $this->assertIsArray($arrResponse);
        $this->assertArrayHasKey(ApiResponse::KEY_ERROR, $arrResponse);
        $this->assertArrayHasKey(ApiResponse::KEY_MESSAGE, $arrResponse);
        $this->assertArrayHasKey(ApiResponse::KEY_DATA, $arrResponse);
        $this->assertTrue($arrResponse[ApiResponse::KEY_ERROR]);
        $this->assertStringContainsString(__('messages.helpers.modelValidation.verifyBeforeSave'), $arrResponse[ApiResponse::KEY_MESSAGE]);

        $arrData = $arrResponse[ApiResponse::KEY_DATA] ?? [];
        $this->assertIsArray($arrData);
        $this->assertArrayHasKey('validator', $arrData);
        $this->assertArrayHasKey('messages', $arrData);

        // validate $arrData['messages'] contains expected error messages
        $this->assertStringContainsString('O campo Name é obrigatório', $arrData['messages']);
        $this->assertStringContainsString('O campo E-mail não contém um endereço de email válido', $arrData['messages']);
        $this->assertStringContainsString('O campo "Telefone" contém um valor inválido', $arrData['messages']);
        $this->assertStringContainsString('O campo "País" contém um valor inválido', $arrData['messages']);
    }
}
