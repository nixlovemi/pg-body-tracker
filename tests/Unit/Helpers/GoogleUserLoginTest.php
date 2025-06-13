<?php

namespace Tests\Unit\Helpers;

use PHPUnit\Framework\TestCase;
use App\Helpers\GoogleUserLogin;

class GoogleUserLoginTest extends TestCase
{
    private array $sampleUser = [
        'id' => '123456789',
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'picture' => 'https://example.com/photo.jpg',
        'given_name' => 'John',
        'family_name' => 'Doe',
    ];

    public function testConstructWithArray()
    {
        $login = new GoogleUserLogin($this->sampleUser);
        $this->assertEquals('123456789', $login->getId());
        $this->assertEquals('John Doe', $login->getName());
        $this->assertEquals('john@example.com', $login->getEmail());
        $this->assertEquals('https://example.com/photo.jpg', $login->getPicture());
        $this->assertEquals('John', $login->getGivenName());
        $this->assertEquals('Doe', $login->getFamilyName());
    }

    public function testConstructWithJsonString()
    {
        $json = json_encode($this->sampleUser);
        $login = new GoogleUserLogin($json);
        $this->assertEquals('123456789', $login->getId());
        $this->assertEquals('John Doe', $login->getName());
        $this->assertEquals('john@example.com', $login->getEmail());
        $this->assertEquals('https://example.com/photo.jpg', $login->getPicture());
        $this->assertEquals('John', $login->getGivenName());
        $this->assertEquals('Doe', $login->getFamilyName());
    }

    public function testMissingFieldsReturnEmptyString()
    {
        $login = new GoogleUserLogin([]);
        $this->assertSame('', $login->getId());
        $this->assertSame('', $login->getName());
        $this->assertSame('', $login->getEmail());
        $this->assertSame('', $login->getPicture());
        $this->assertSame('', $login->getGivenName());
        $this->assertSame('', $login->getFamilyName());
    }
}
