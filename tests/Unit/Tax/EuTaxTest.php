<?php
/**
 * Invoice Ninja (https://invoiceninja.com).
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2021. Invoice Ninja LLC (https://invoiceninja.com)
 *
 * @license https://www.elastic.co/licensing/elastic-license
 */

namespace Tests\Unit\Tax;

use Tests\TestCase;
use App\Models\Client;
use App\Models\Company;
use Tests\MockAccountData;
use App\DataMapper\Tax\de\Rule;
use App\DataMapper\Tax\TaxModel;
use App\DataMapper\CompanySettings;
use App\Services\Tax\Providers\EuTax;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * @test App\Services\Tax\Providers\EuTax
 */
class EuTaxTest extends TestCase
{
    use MockAccountData;
    use DatabaseTransactions;
    
    protected function setUp() :void
    {
        parent::setUp();

        $this->withoutMiddleware(
            ThrottleRequests::class
        );

        $this->withoutExceptionHandling();

        $this->makeTestData();
    }

    public function testCorrectRuleInit()
    {

        $settings = CompanySettings::defaults();
        $settings->country_id = '276'; // germany

        $tax_data = new TaxModel();
        $tax_data->seller_region = 'DE';
        $tax_data->seller_subregion = 'DE';
        $tax_data->regions->EU->has_sales_above_threshold = true;
        $tax_data->regions->EU->tax_all = true;
        
        $company = Company::factory()->create([
            'account_id' => $this->account->id,
            'settings' => $settings,
            'tax_data' => $tax_data,
        ]);

        $client = Client::factory()->create([
            'user_id' => $this->user->id,
            'company_id' => $company->id,
            'country_id' => 276,
            'shipping_country_id' => 276,
        ]);

        $process = new EuTax($company, $client);
        $process->run();

        $this->assertEquals('de', $process->getVendorCountryCode());

        $this->assertEquals('de', $process->getClientCountryCode());

        $this->assertFalse($process->hasValidVatNumber());

        $this->assertInstanceOf(Rule::class, $process->rule);

        $this->assertEquals(19, $process->getVatRate());

        $this->assertEquals(7, $process->getVatReducedRate());


    }
    
    public function testEuCorrectRuleInit()
    {

        $settings = CompanySettings::defaults();
        $settings->country_id = '276'; // germany

        $tax_data = new TaxModel();
        $tax_data->seller_region = 'DE';
        $tax_data->seller_subregion = 'DE';
        $tax_data->regions->EU->has_sales_above_threshold = true;
        $tax_data->regions->EU->tax_all = true;

        $company = Company::factory()->create([
            'account_id' => $this->account->id,
            'settings' => $settings,
            'tax_data' => $tax_data,
        ]);


        $client = Client::factory()->create([
            'user_id' => $this->user->id,
            'company_id' => $company->id,
            'country_id' => 56,
            'shipping_country_id' => 56,
        ]);

        $process = new EuTax($company, $client);
        $process->run();

        $this->assertEquals('de', $process->getVendorCountryCode());

        $this->assertEquals('be', $process->getClientCountryCode());

        $this->assertFalse($process->hasValidVatNumber());

        $this->assertInstanceOf(Rule::class, $process->rule);

        $this->assertEquals(21, $process->getVatRate());

        $this->assertEquals(6, $process->getVatReducedRate());


    }

    public function testForeignCorrectRuleInit()
    {

        $settings = CompanySettings::defaults();
        $settings->country_id = '276'; // germany

        $company = Company::factory()->create([
            'account_id' => $this->account->id,
            'settings' => $settings
        ]);

        $client = Client::factory()->create([
            'user_id' => $this->user->id,
            'company_id' => $company->id,
            'country_id' => 840,
            'shipping_country_id' => 840,
        ]);

        $process = new EuTax($company, $client);
        $process->run();

        $this->assertEquals('de', $process->getVendorCountryCode());

        $this->assertEquals('us', $process->getClientCountryCode());

        $this->assertFalse($process->hasValidVatNumber());

        $this->assertInstanceOf(Rule::class, $process->rule);

        $this->assertEquals(0, $process->getVatRate());

        $this->assertEquals(0, $process->getVatReducedRate());


    }


}
