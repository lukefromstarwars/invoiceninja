<?php
/**
 * Invoice Ninja (https://invoiceninja.com).
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2022. Invoice Ninja LLC (https://invoiceninja.com)
 *
 * @license https://www.elastic.co/licensing/elastic-license
 */

namespace App\Transformers;

use App\Models\Account;
use App\Models\BankIntegration;
use App\Utils\Traits\MakesHash;

/**
 * Class BankIntegrationTransformer.
 */
class BankIntegrationTransformer extends EntityTransformer
{
    use MakesHash;

    /**
     * @var array
     */
    protected $defaultIncludes = [
        //'default_company',
        //'user',
        //'company_users'
    ];

    /**
     * @var array
     */
    protected $availableIncludes = [
        'company',
        'account',
    ];

    /**
     * @param Account $bank_integration
     *
     *
     * @return array
     */
    public function transform(BankIntegration $bank_integration)
    {
        return [
            'id' => (string) $this->encodePrimaryKey($bank_integration->id),
            'provider_bank_name' => $bank_integration->provider_bank_name ?: '',
            'bank_account_id' => $bank_integration->bank_account_id ?: '',
            'bank_account_name' => $bank_integration->bank_account_name ?: '',
            'bank_account_number' => $bank_integration->bank_account_number ?: '',
            'bank_account_status' => $bank_integration->bank_account_status ?: '',
            'bank_account_type' => $bank_integration->bank_account_type ?: '',
            'balance' => (float)$bank_integration->balance ?: 0,
            'currency' => $bank_integration->currency ?: '',
        ];
    }

    public function includeAccount(BankIntegration $bank_integration)
    {
        $transformer = new AccountTransformer($this->serializer);

        return $this->includeItem($bank_integration->account, $transformer, Account::class);
    }

    public function includeCompany(BankIntegration $bank_integration)
    {
        $transformer = new CompanyTransformer($this->serializer);

        return $this->includeItem($bank_integration->company, $transformer, Company::class);
    }

}
