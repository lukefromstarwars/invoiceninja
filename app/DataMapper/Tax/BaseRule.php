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

    public function setClient(Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function setTaxData(Response $tax_data): self
    {
        $this->tax_data = $tax_data;

        return $this;
    }

    public function tax(): self
    {
        return $this;
    }

    public function taxByType(?int $product_tax_type): self
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
}
