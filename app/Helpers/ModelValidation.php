<?php
# https://laravel.com/docs/8.x/validation

namespace App\Helpers;

use Illuminate\Support\Facades\Validator;
use App\Helpers\Country;
use App\Helpers\ApiResponse;

class ModelValidation
{
    private const F_RULES = 'rules';
    private const F_ATTR_LABEL = 'attributeLabel';

    private array $_data = [];
    private array $_validationData = [];

    public function __construct(array $validationData)
    {
        $this->_validationData = $validationData;
    }

    public function addField(
        string $fieldName,
        array $rules,
        string $attributeLabel = ''
    ): void {
        $this->_data[$fieldName] = [
            self::F_RULES => $rules,
            self::F_ATTR_LABEL => [$fieldName => (empty($attributeLabel)) ? $fieldName: $attributeLabel]
        ];
    }

    public function addIdField(
        string $modelClass,
        string $modelLabel,
        string $fieldName,
        string $attributeLabel = '',
        array $customRules=[]
    ): void {
        $this->addField($fieldName, array_merge([
            'nullable', 'integer', 'gt:0', function ($attribute, $value, $fail) use ($modelClass, $modelLabel) {
                if (empty($value)) {
                    return;
                }

                if (false === $modelClass::find($value)?->exists()) {
                    $fail("{$modelLabel} não encontrado!");
                }
            }
        ], $customRules), $attributeLabel);
    }

    public function addPhoneField(string $fieldName, string $attributeLabel = '', array $customRules=[]): void
    {
        $label = $this->getLabel($fieldName, $attributeLabel);
        $this->addField($fieldName, array_merge([function ($attribute, $value, $fail) use ($label) {
            $phone = trim($value);
            if (empty($phone)) {
                return;
            }

            if (1 !== preg_match(Constants::REGEX_PHONE_NUMBER, $phone) ) {
                $fail("O campo {$label} é inválido!");
            }
        }], $customRules), $attributeLabel);
    }

    public function addEmailField(string $fieldName, string $attributeLabel = '', array $customRules=[]): void
    {
        $label = $this->getLabel($fieldName, $attributeLabel);
        $this->addField($fieldName, array_merge(['email:filter'], $customRules), $attributeLabel);
    }

    public function addProvinceField(string $fieldName, string $attributeLabel = '', array $customRules=[]): void
    {
        $label = $this->getLabel($fieldName, $attributeLabel);
        $this->addField($fieldName, array_merge(['string', 'min:2', 'max:255', function ($attribute, $value, $fail) use ($label) {
            if (empty($value)) {
                return;
            }

            $countries = Country::getCountries();
            $found = false;
            foreach ($countries as $country) {
                $provinces = Country::getProvinceByCountry($country);
                if (array_key_exists($value, $provinces)) {
                    $found = true;
                    break;
                }
            }

            if (false === $found) {
                $fail("O campo {$label} contém um valor inválido!");
            }
        }], $customRules), $attributeLabel);
    }

    public function addCountryField(string $fieldName, string $attributeLabel = '', array $customRules=[]): void
    {
        $label = $this->getLabel($fieldName, $attributeLabel);
        $this->addField($fieldName, array_merge(['string', 'min:2', 'max:255', function ($attribute, $value, $fail) use ($label) {
            if (empty($value)) {
                return;
            }

            $countries = Country::getCountries();
            if (false === array_search($value, $countries)) {
                $fail("O campo {$label} contém um valor inválido!");
            }
        }], $customRules), $attributeLabel);
    }

    public function validate(): ApiResponse
    {
        $validator = Validator::make(
            $this->_validationData,
            $this->getRulesArray(),
            $this->getDefaultMessages(),
            $this->getCustomAttrArray()
        );

        $error = $validator->fails();
        $message = $error ? 'Verifique antes de salvar!': 'Dados validados com sucesso!';
        $data = [
            'validator' => $validator,
            'messages' => 'Verifique antes de continuar!<br />* ' . implode('<br />* ', $validator->errors()->all()),
        ];

        return new ApiResponse(
            $error,
            $message,
            $data
        );
    }

    private function getRulesArray(): array
    {
        $rules = [];
        foreach ($this->_data as $fieldName => $data) {
            $rules[$fieldName] = $data[self::F_RULES] ?? [];
        }

        return $rules;
    }

    private function getCustomAttrArray(): array
    {
        $customAttr = [];
        foreach ($this->_data as $fieldName => $data) {
            foreach ($data[self::F_ATTR_LABEL] as $key => $val) {
                $customAttr[$key] = $val;
            }
        }

        return $customAttr;
    }

    private function getDefaultMessages(): array
    {
        return [
            'required' => 'O campo ":attribute" é obrigatório',
            'integer' => 'O campo ":attribute" deve ser do tipo inteiro',
            'gt' => 'O campo ":attribute" deve ser maior que :value',
            'string' => 'O campo ":attribute" deve ser do tipo texto',
            'url' => 'O campo ":attribute" deve ser uma URL válida',
            'min' => 'O campo ":attribute" deve ter no mínimo :min caracteres.',
            'max' => 'O campo ":attribute" deve ter no máximo :max caracteres.',
            'email' => 'O campo ":attribute" deve conter um e-mail válido.',
            'between' => 'O campo ":attribute" deve ser entre :min e :max.',
        ];
    }

    private function getLabel(string $fieldName, string $attributeLabel = ''): string
    {
        return (empty($attributeLabel)) ? $fieldName: $attributeLabel;
    }
}