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

namespace App\DataMapper\Tax;

use App\DataMapper\Tax\ZipTax\Response;
use App\Models\Client;

class BaseRule implements RuleInterface
{
    /** EU TAXES */
    public bool $consumer_tax_exempt = false;

    public bool $business_tax_exempt = true;

    public bool $eu_business_tax_exempt = true;

    public bool $foreign_business_tax_exempt = true;

    public bool $foreign_consumer_tax_exempt = true;

    public string $vendor_iso_3166_2 = '';

    public string $client_iso_3166_2 = '';

    public string $client_region = '';

    public array $eu_country_codes = [
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

    public array $region_codes = [ 
            'AT' => 'EU', // Austria
            'BE' => 'EU', // Belgium
            'BG' => 'EU', // Bulgaria
            'CY' => 'EU', // Cyprus
            'CZ' => 'EU', // Czech Republic
            'DE' => 'EU', // Germany
            'DK' => 'EU', // Denmark
            'EE' => 'EU', // Estonia
            'ES' => 'EU', // Spain
            'FI' => 'EU', // Finland
            'FR' => 'EU', // France
            'GR' => 'EU', // Greece
            'HR' => 'EU', // Croatia
            'HU' => 'EU', // Hungary
            'IE' => 'EU', // Ireland
            'IT' => 'EU', // Italy
            'LT' => 'EU', // Lithuania
            'LU' => 'EU', // Luxembourg
            'LV' => 'EU', // Latvia
            'MT' => 'EU', // Malta
            'NL' => 'EU', // Netherlands
            'PL' => 'EU', // Poland
            'PT' => 'EU', // Portugal
            'RO' => 'EU', // Romania
            'SE' => 'EU', // Sweden
            'SI' => 'EU', // Slovenia
            'SK' => 'EU', // Slovakia
        
            'US' => 'US', // United States
    ];

    /** EU TAXES */


    /** US TAXES */
    /** US TAXES */

    public string $tax_name1 = '';
    public float $tax_rate1 = 0;

    public string $tax_name2 = '';
    public float $tax_rate2 = 0;

    public string $tax_name3 = '';
    public float $tax_rate3 = 0;

    protected ?Client $client;

    protected ?Response $tax_data;

    public function __construct()
    {
    }

    public function init(): self
    {
        return $this;
    }

    public function setClient(Client $client): self
    {
        $this->client = $client;

        $this->resolveRegions();

        return $this;
    }

    private function resolveRegions(): self
    {

        if(!array_key_exists($this->client->country->iso_3166_2, $this->region_codes))
            throw new \Exception('Country not supported');
            
        $this->client_region = $this->region_codes[$this->client->country->iso_3166_2] ?? '';

        $this->client_subregion = 

        return $this;
    }

    public function setTaxData(Response $tax_data): self
    {
        $this->tax_data = $tax_data;

        return $this;
    }

    public function tax($product_tax_type): self
    {
        return $this;
    }

    public function taxByType($product_tax_type): self
    {
        return $this;
    }

    public function taxReduced(): self
    {
        return $this;
    }

    public function taxExempt(): self
    {
        return $this;
    }

    public function taxDigital(): self
    {
        return $this;
    }

    public function taxService(): self
    {
        return $this;
    }

    public function taxShipping(): self
    {
        return $this;
    }

    public function taxPhysical(): self
    {
        return $this;
    }

    public function default(): self
    {
        return $this;
    }

    public function override(): self
    {
        return $this;
    }

    public function calculateRates(): self
    {
        return $this;
    }
}
