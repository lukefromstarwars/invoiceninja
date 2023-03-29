<?php
/**
 * Invoice Ninja (https://invoiceninja.com).
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2023. Invoice Ninja LLC (https://invoiceninja.com)
 *
 * @license https://www.elastic.co/licensing/elastic-license
 */

namespace App\Services\Tax\Providers;

use App\Models\Client;
use App\Models\Company;
use Illuminate\Support\Str;
use App\DataMapper\Tax\de\Rule;
use App\Services\Tax\VatNumberCheck;
class EuTax
{
    public Rule $rule;

    private string $vendor_country_code;

    private string $client_country_code;

    private bool $valid_vat_number = false;

    private float $vat_rate = 0.0;

    private float $reduced_vat_rate = 0.0;

    private array $eu_country_codes = [
        'AT', // Austria
        'BE', // Belgium
        'BG', // Bulgaria
        'CY', // Cyprus
        'CZ', // Czech Republic
        'DE', // Germany
        'DK', // Denmark
        'EE', // Estonia
        'ES', // Spain
        'FI', // Finland
        'FR', // France
        'GR', // Greece
        'HR', // Croatia
        'HU', // Hungary
        'IE', // Ireland
        'IT', // Italy
        'LT', // Lithuania
        'LU', // Luxembourg
        'LV', // Latvia
        'MT', // Malta
        'NL', // Netherlands
        'PL', // Poland
        'PT', // Portugal
        'RO', // Romania
        'SE', // Sweden
        'SI', // Slovenia
        'SK', // Slovakia
    ];



    public function __construct(protected Company $company, protected Client $client)
    {
    }
    
    /* need to have a setting that allows a user to define their annual turnover, or whether they have breached their thresholds */
    public function run()
    {
        $this->setUp()
             ->validateVat()
             ->calculateVatRates();
    }

    public function hasValidVatNumber(): bool
    {
        return $this->valid_vat_number;
    }

    public function getVatRate(): float
    {
        return $this->vat_rate;
    }

    public function getVatReducedRate(): float
    {
        return $this->reduced_vat_rate;
    }

    public function getVendorCountryCode(): string
    {
        return $this->vendor_country_code;
    }

    public function getClientCountryCode(): string
    {
        return $this->client_country_code;
    }

    private function setUp(): self
    {
        $this->vendor_country_code = Str::lower($this->company->country()->iso_3166_2);

        $this->client_country_code = $this->client->shipping_country ? Str::lower($this->client->shipping_country->iso_3166_2) : Str::lower($this->client->country->iso_3166_2);

        $class = "App\\DataMapper\\Tax\\".$this->vendor_country_code."\\Rule";

        $this->rule = new $class();

        return $this;
    }

    private function validateVat(): self
    {
        $vat_check = (new VatNumberCheck($this->client->vat_number, $this->client_country_code))->run();

        $this->valid_vat_number = $vat_check->isValid();

        return $this;
    }

    private function calculateVatRates(): self
    {

        if(
            (($this->vendor_country_code == $this->client_country_code) && $this->valid_vat_number && $this->rule->business_tax_exempt) || //same country / exempt for tax / valid vat number
            (in_array($this->client_country_code, $this->eu_country_codes) && $this->valid_vat_number && $this->rule->eu_business_tax_exempt) //eu country / exempt for tax / valid vat number
        ) {
            $this->vat_rate = 0;
            $this->reduced_vat_rate = 0;
            nlog("euro zone and tax exempt");
        }
        elseif(!in_array(strtoupper($this->client_country_code), $this->eu_country_codes) && ($this->rule->foreign_consumer_tax_exempt || $this->rule->foreign_business_tax_exempt)) //foreign + tax exempt
        {
            $this->vat_rate = 0;
            $this->reduced_vat_rate = 0;
            nlog("foreign and tax exempt");
        }
        elseif(in_array(strtoupper($this->client_country_code), $this->eu_country_codes) && !$this->valid_vat_number) //eu country / no valid vat 
        {   
            if(($this->vendor_country_code != $this->client_country_code) && $this->company->tax_data->regions->EU->has_sales_above_threshold)
            {
                $this->vat_rate = $this->company->tax_data->regions->EU->subregions->{$this->client->country->iso_3166_2}->vat_rate;
                $this->reduced_vat_rate = $this->company->tax_data->regions->EU->subregions->{$this->client->country->iso_3166_2}->reduced_vat_rate;
                nlog("eu zone with sales above threshold");
            }
            else {
                $this->vat_rate = $this->company->tax_data->regions->EU->subregions->{$this->company->country()->iso_3166_2}->vat_rate;
                $this->reduced_vat_rate = $this->company->tax_data->regions->EU->subregions->{$this->company->country()->iso_3166_2}->reduced_vat_rate;
                nlog("same eu country with");
            }
        }
        else {
            nlog("default tax");
            $this->vat_rate = $this->company->tax_data->regions->EU->subregions->{$this->company->country()->iso_3166_2}->vat_rate;
            $this->reduced_vat_rate = $this->company->tax_data->regions->EU->subregions->{$this->company->country()->iso_3166_2}->reduced_vat_rate;
        }

        return $this;

    }
}

