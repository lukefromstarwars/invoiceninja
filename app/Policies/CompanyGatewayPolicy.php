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

namespace App\Policies;

use App\Models\User;

/**
 * Class ClientPolicy.
 */
class CompanyGatewayPolicy extends EntityPolicy
{
    /**
     *  Checks if the user has create permissions.
     *
     * @param  User $user
     * @return bool
     */
    public function create(User $user) : bool
    {
        return $user->isAdmin();
    }
}
