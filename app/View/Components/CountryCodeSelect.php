<?php

namespace App\View\Components;

use Illuminate\View\Component;

class CountryCodeSelect extends Component
{
    public array $countryCodes = [];

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        public string $selectedValue = '55',
        public string $name = 'country_code',
        public string $id = 'country_code',
        public bool $disabled = false,
    ) {
        $this->countryCodes = $this->getCountryCodes();
    }

    private function getCountryCodes(): array
    {
        return [
            'USA' => '+1',
            'CAN' => '+1',
            'BRA' => '+55',
            'GBR' => '+44',
            'AUS' => '+61',
            'IND' => '+91',
            'FRA' => '+33',
            'DEU' => '+49',
            'ITA' => '+39',
            'ESP' => '+34',
            'MEX' => '+52',
            'ARG' => '+54',
            'CHN' => '+86',
            'JPN' => '+81',
            'KOR' => '+82',
            'RUS' => '+7',
            'ZAF' => '+27',
            'EGY' => '+20',
            'TUR' => '+90',
            'SAU' => '+966',
            'NLD' => '+31',
            'SWE' => '+46',
            'NOR' => '+47',
            'DNK' => '+45',
            'CHE' => '+41',
            'COL' => '+57',
            'PER' => '+51',
            'CHL' => '+56',
            'POL' => '+48',
            'UKR' => '+380',
            'PRT' => '+351',
            'IRL' => '+353',
            'GRC' => '+30',
            'ISR' => '+972',
            'THA' => '+66',
            'VNM' => '+84',
            'PHL' => '+63',
            'IDN' => '+62',
            'PAK' => '+92',
            'NZL' => '+64',
            'SGP' => '+65',
            'HKG' => '+852',
            'TWN' => '+886',
            'ARE' => '+971',
            'MAR' => '+212',
            'KEN' => '+254',
            'NGA' => '+234',
            'CMR' => '+237',
            'CRI' => '+506',
            'CUB' => '+53',
            'VEN' => '+58',
            'URY' => '+598',
            'ECU' => '+593',
            'BOL' => '+591',
            'PAR' => '+595',
            'DOM' => '+1-809', // República Dominicana
            'PAN' => '+507',
            'HND' => '+504',
            'GTM' => '+502',
            'NIC' => '+505',
        ];
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.country-code-select');
    }
}
