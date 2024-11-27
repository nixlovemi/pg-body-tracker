<?php

namespace App\Helpers;

final class Country {
    public const C_BRASIL = 'Brasil';
    public const C_USA = 'USA';

    private const DATA = [
        self::C_BRASIL => [
            'AC' => 'Acre',
            'AL' => 'Alagoas',
            'AP' => 'Amapá',
            'AM' => 'Amazonas',
            'BA' => 'Bahia',
            'CE' => 'Ceará',
            'ES' => 'Espírito Santo',
            'GO' => 'Goiás',
            'MA' => 'Maranhão',
            'MT' => 'Mato Grosso',
            'MS' => 'Mato Grosso do Sul',
            'MG' => 'Minas Gerais',
            'PA' => 'Pará',
            'PB' => 'Paraíba',
            'PR' => 'Paraná',
            'PE' => 'Pernambuco',
            'PI' => 'Piauí',
            'RJ' => 'Rio de Janeiro',
            'RN' => 'Rio Grande do Norte',
            'RS' => 'Rio Grande do Sul',
            'RO' => 'Rondônia',
            'RR' => 'Roraima',
            'SC' => 'Santa Catarina',
            'SP' => 'São Paulo',
            'SE' => 'Sergipe',
            'TO' => 'Tocantins',
            'DF' => 'Distrito Federal',
        ],
        self::C_USA => [
            'AL' => 'Alabama',
            'AK' => 'Alasca',
            'AR' => 'Arcansas',
            'AZ' => 'Arizona',
            'CA' => 'Califórnia',
            'KS' => 'Cansas',
            'NC' => 'Carolina do Norte',
            'SC' => 'Carolina do Sul',
            'CO' => 'Colorado',
            'CT' => 'Conecticute',
            'ND' => 'Dakota do Norte',
            'SD' => 'Dakota do Sul',
            'DE' => 'Delaware',
            'FL' => 'Flórida',
            'GA' => 'Geórgia',
            'HI' => 'Havaí',
            'ID' => 'Idaho',
            'RI' => 'Ilha de Rodes',
            'IL' => 'Ilinóis',
            'IN' => 'Indiana',
            'IA' => 'Iowa',
            'KY' => 'Kentucky',
            'LA' => 'Luisiana',
            'ME' => 'Maine',
            'MD' => 'Marilândia',
            'MA' => 'Massachusetts',
            'MI' => 'Michigan',
            'MN' => 'Minesota',
            'MS' => 'Mississípi',
            'MO' => 'Missúri',
            'MT' => 'Montana',
            'NE' => 'Nebrasca',
            'NV' => 'Nevada',
            'NH' => 'Nova Hampshire',
            'NJ' => 'Nova Jérsei',
            'NY' => 'Nova Iorque',
            'NM' => 'Novo México',
            'OK' => 'Oclaoma',
            'OH' => 'Ohio',
            'OR' => 'Óregon',
            'PA' => 'Pensilvânia',
            'TN' => 'Tenessi',
            'TX' => 'Texas',
            'UT' => 'Utá',
            'VT' => 'Vermonte',
            'VA' => 'Virgínia',
            'WV' => 'Virgínia Ocidental',
            'WA' => 'Washington',
            'WI' => 'Wiscosin',
            'WY' => 'Wyoming',
        ]
    ];

    public static function getCountries(): array
    {
        return array_keys(self::DATA);
    }

    public static function getProvinceByCountry(string $country): array
    {
        return self::DATA[$country] ?? [];
    }
}
