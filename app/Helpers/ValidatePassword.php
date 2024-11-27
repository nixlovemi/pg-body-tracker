<?php

namespace App\Helpers;

use App\Helpers\ApiResponse;

class ValidatePassword {
    public const RULES = [
        [
            'text' => 'Mínimo de 8 caracteres',
            'fnc' => 'checkMinChar'
        ],
        [
            'text' => 'Letras e números',
            'fnc' => 'checkHasNumber'
        ],
    ];

    public function __construct(private string $_password = '') { }

    public function validate(): ApiResponse
    {
        $ret = [];
        foreach ($this::RULES as $rules) {
            $methodToCheck = $rules['fnc'];
            if (false === method_exists($this, $methodToCheck)) {
                continue;
            }

            if (false === $this->$methodToCheck()) {
                $ret[] = $rules['text'];
            }
        }

        $hasErrors = count($ret) > 0;
        return new ApiResponse(
            $hasErrors,
            $hasErrors ? 'Senha não é válida. Verifique:<br />- ' . implode('<br />- ', $ret): 'Senha validada com sucesso!',
            [
                'ret' => $ret
            ]
        );
    }

    public static function getRulesTexts(): array
    {
        return array_column(self::RULES, 'text');
    }

    private function checkMinChar(): bool
    {
        return strlen($this->_password) >= 8;
    }

    private function checkHasNumber(): bool
    {
        return preg_match('@[0-9]@', $this->_password);
    }
}
