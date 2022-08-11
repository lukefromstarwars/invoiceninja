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
     * @param BankIntegration $bank_integration
     * @return array
     */
    public function transform(BankIntegration $bank_integration)
    {
        return [
            'id' => (string) $this->encodePrimaryKey($bank_integration->id),
            'provider_name' => (string)$bank_integration->provider_name ?: '',
            'provider_id' => (int) $bank_integration->provider_id ?: 0,
            'bank_account_id' => (int) $bank_integration->bank_account_id ?: 0,
            'bank_account_name' => (string) $bank_integration->bank_account_name ?: '',
            'bank_account_number' => (string) $bank_integration->bank_account_number ?: '',
            'bank_account_status' => (string)$bank_integration->bank_account_status ?: '',
            'bank_account_type' => (string)$bank_integration->bank_account_type ?: '',
            'balance' => (float)$bank_integration->balance ?: 0,
            'currency' => (string)$bank_integration->currency ?: '',
            'nickname' => (string)$bank_integration->nickname ?: '',
            'is_deleted' => (bool) $bank_integration->is_deleted,
            'created_at' => (int) $bank_integration->created_at,
            'updated_at' => (int) $bank_integration->updated_at,
            'archived_at' => (int) $bank_integration->deleted_at,
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
