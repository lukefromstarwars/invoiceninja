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

namespace App\DataMapper\Tax\us;

use App\Models\Client;
use App\Models\Product;
use App\DataMapper\Tax\RuleInterface;
use App\DataMapper\Tax\ZipTax\Response;

class Rule implements RuleInterface
{

    public string $tax_name1 = '';
    public float $tax_rate1 = 0;

    public string $tax_name2 = '';
    public float $tax_rate2 = 0;
    
    public string $tax_name3 = '';
    public float $tax_rate3 = 0;
    
    public ?Client $client;

    public ?Response $tax_data;

    public function __construct()
    {
    }

    public function override() 
    { 
        return $this;
    }

    public function setTaxData(Response $tax_data): self
    {
        $this->tax_data = $tax_data;

        return $this;
    }

    public function setClient(Client $client):self 
    {
        $this->client = $client;

        return $this;
    }

    public function tax(): self
    {
        if($this->client->is_tax_exempt)
            return $this->taxExempt();

        $this->tax_rate1 = $this->tax_data->taxSales * 100;
        $this->tax_name1 = "{$this->tax_data->geoState} Sales Tax";

        return $this;

    }

    public function taxByType(?int $product_tax_type): self
    {
        if(!$product_tax_type)
            return $this;


        if ($this->client->is_tax_exempt) {
            return $this->taxExempt();
        }

        match($product_tax_type){
            Product::PRODUCT_TYPE_EXEMPT => $this->taxExempt(),
            Product::PRODUCT_TYPE_DIGITAL => $this->taxDigital(),
            Product::PRODUCT_TYPE_SERVICE => $this->taxService(),
            Product::PRODUCT_TYPE_SHIPPING => $this->taxShipping(),
            Product::PRODUCT_TYPE_PHYSICAL => $this->taxPhysical(),
            Product::PRODUCT_TYPE_REDUCED_TAX => $this->taxReduced(),
            Product::PRODUCT_TYPE_OVERRIDE_TAX => $this->override(),
            default => $this->default(),
        };
        
        return $this;
    }

    public function taxExempt(): self
    {
        $this->tax_name1 = '';
        $this->tax_rate1 = 0;

        return $this;
    }

    public function taxDigital(): self
    {
        $this->tax();

        return $this;
    }

    public function taxService(): self
    {
        if($this->tax_data->txbService == 'Y')
            $this->tax();

        return $this;
    }

    public function taxShipping(): self
    {
        if($this->tax_data->txbFreight == 'Y')
            $this->tax();

        return $this;
    }

    public function taxPhysical(): self
    {
        $this->tax();

        return $this;
    }

    public function default(): self
    {
        
        $this->tax_name1 = 'Tax Exempt';
        $this->tax_rate1 = 0;

        return $this;
    }

    public function taxReduced(): self
    {
        $this->tax();

        return $this;
    }
}
