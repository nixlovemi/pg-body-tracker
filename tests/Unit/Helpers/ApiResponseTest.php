<?php

namespace Tests\Unit\Traits;

use PHPUnit\Framework\TestCase;
use App\Helpers\ApiResponse;

class ApiResponseTest extends TestCase
{
    public function testIsErrorReturnsTrueWhenErrorIsTrue()
    {
        $response = new ApiResponse(true, 'Error occurred');
        $this->assertTrue($response->isError());
    }

    public function testIsErrorReturnsFalseWhenErrorIsFalse()
    {
        $response = new ApiResponse(false, 'Success');
        $this->assertFalse($response->isError());
    }

    public function testGetMessageReturnsMessage()
    {
        $response = new ApiResponse(false, 'Test message');
        $this->assertEquals('Test message', $response->getMessage());
    }

    public function testGetArrayResponseWithoutData()
    {
        $response = new ApiResponse(false, 'No data');
        $expected = [
            'error' => false,
            'message' => 'No data',
        ];
        $this->assertEquals($expected, $response->getArrayResponse());
    }

    public function testGetArrayResponseWithData()
    {
        $data = ['foo' => 'bar'];
        $response = new ApiResponse(false, 'With data', $data);
        $expected = [
            'error' => false,
            'message' => 'With data',
            'data' => $data,
        ];
        $this->assertEquals($expected, $response->getArrayResponse());
    }

    public function testGetValueFromResponseReturnsValueIfExists()
    {
        $data = ['foo' => 'bar'];
        $response = new ApiResponse(false, 'msg', $data);
        $this->assertEquals('bar', $response->getValueFromResponse('foo'));
    }

    public function testGetValueFromResponseReturnsNullIfKeyNotExists()
    {
        $data = ['foo' => 'bar'];
        $response = new ApiResponse(false, 'msg', $data);
        $this->assertNull($response->getValueFromResponse('baz'));
    }

    public function testGetValueFromResponseReturnsNullIfNoData()
    {
        $response = new ApiResponse(false, 'msg');
        $this->assertNull($response->getValueFromResponse('foo'));
    }

    public function testGetValidateMessageReturnsMessagesKeyIfExists()
    {
        $data = ['messages' => 'Validation failed'];
        $response = new ApiResponse(true, 'Fallback message', $data);
        $this->assertEquals('Validation failed', ApiResponse::getValidateMessage($response));
    }

    public function testGetValidateMessageReturnsMessageIfNoMessagesKey()
    {
        $response = new ApiResponse(true, 'Fallback message');
        $this->assertEquals('Fallback message', ApiResponse::getValidateMessage($response));
    }
}
