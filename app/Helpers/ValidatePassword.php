<?php

namespace App\Helpers;

use App\Helpers\ApiResponse;

class ValidatePassword {

    public function __construct(private string $_password = '') { }

    public function validate(): ApiResponse
    {
        $ret = [];
        $rules = self::getRulesArray();
        foreach ($rules as $rule) {
            $methodToCheck = $rule['fnc'];
            if (false === method_exists($this, $methodToCheck)) {
                continue;
            }

            if (false === $this->$methodToCheck()) {
                $ret[] = $rule['text'];
            }
        }

        $hasErrors = count($ret) > 0;
        return new ApiResponse(
            $hasErrors,
            $this->getValidationMsg($hasErrors, $ret),
            [
                'ret' => $ret
            ]
        );
    }

    public static function getRulesTexts(): array
    {
        return array_column(self::getRulesArray(), 'text');
    }

    private function checkMinChar(): bool
    {
        return strlen($this->_password) >= 8;
    }

    private function checkHasNumber(): bool
    {
        return preg_match('@[0-9]@', $this->_password);
    }

    private function getValidationMsg(bool $hasErrors, array $ret): string
    {
        if ($hasErrors) {
            return __('messages.components.ValidatePassword.notValidHtml', [
                'text' => ' -' . implode('<br />- ', $ret)
            ]);
        }

        return __('messages.components.ValidatePassword.validHtml');
    }

    public static function getRulesArray(): array
    {
        return [
            [
                'text' => __('messages.components.ValidatePassword.minChar'),
                'fnc' => 'checkMinChar'
            ],
            [
                'text' => __('messages.components.ValidatePassword.hasNumber'),
                'fnc' => 'checkHasNumber'
            ],
        ];
    }
}
