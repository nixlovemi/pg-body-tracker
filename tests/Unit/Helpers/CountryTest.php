<?php

namespace Tests\Unit\Helpers;

use PHPUnit\Framework\TestCase;
use App\Helpers\Country;

class CountryTest extends TestCase
{
    public function testGetCountriesReturnsAllCountries()
    {
        $countries = Country::getCountries();
        $this->assertIsArray($countries);
        $this->assertContains(Country::C_BRASIL, $countries);
        $this->assertContains(Country::C_USA, $countries);
        $this->assertCount(2, $countries);
    }

    public function testGetProvinceByCountryReturnsBrazilStates()
    {
        $provinces = Country::getProvinceByCountry(Country::C_BRASIL);
        $this->assertIsArray($provinces);
        $this->assertArrayHasKey('SP', $provinces);
        $this->assertEquals('São Paulo', $provinces['SP']);
        $this->assertArrayHasKey('RJ', $provinces);
        $this->assertEquals('Rio de Janeiro', $provinces['RJ']);
        $this->assertCount(27, $provinces); // 26 states + DF
    }

    public function testGetProvinceByCountryReturnsUSAStates()
    {
        $provinces = Country::getProvinceByCountry(Country::C_USA);
        $this->assertIsArray($provinces);
        $this->assertArrayHasKey('CA', $provinces);
        $this->assertEquals('Califórnia', $provinces['CA']);
        $this->assertArrayHasKey('NY', $provinces);
        $this->assertEquals('Nova Iorque', $provinces['NY']);
        $this->assertCount(50, $provinces); // 50 states
    }

    public function testGetProvinceByCountryReturnsEmptyArrayForUnknownCountry()
    {
        $provinces = Country::getProvinceByCountry('UnknownCountry');
        $this->assertIsArray($provinces);
        $this->assertEmpty($provinces);
    }
}
