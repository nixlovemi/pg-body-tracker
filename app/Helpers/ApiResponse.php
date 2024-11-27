<?php

namespace App\Helpers;

class ApiResponse
{
    const KEY_ERROR = 'error';
    const KEY_MESSAGE = 'message';
    const KEY_DATA = 'data';

    public function __construct(
        private bool $error,
        private string $message,
        private array $data = []
    ) { }

    public function isError(): bool
    {
        return true === $this->error;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Returns an array for Api Response
     *
     * @param boolean $error    [true | false]
     * @param string $message   [the message]
     * @param array $data       [array of data]
     * @return array
     */
    public function getArrayResponse(): array
    {
        $arrRet = array(
            self::KEY_ERROR => $this->error,
            self::KEY_MESSAGE => $this->message,
        );

        if (is_array($this->data) && count($this->data) > 0) {
            $arrRet[self::KEY_DATA] = $this->data;
        }

        return $arrRet;
    }

    /**
     * Returns null if not found
     */
    public function getValueFromResponse(string $key)
    {
        $arrResponse = $this->getArrayResponse();
        if (empty($arrResponse)) {
            return null;
        }

        $arrData = $arrResponse[self::KEY_DATA] ?? [];
        if (empty($arrData)) {
            return null;
        }

        if (false === array_key_exists($key, $arrData)) {
            return null;
        }

        return $arrData[$key];
    }

    public static function getValidateMessage(ApiResponse $validate): string
    {
        $dataMessage = $validate->getValueFromResponse('messages');
        return $dataMessage ?? $validate->getMessage();
    }
}