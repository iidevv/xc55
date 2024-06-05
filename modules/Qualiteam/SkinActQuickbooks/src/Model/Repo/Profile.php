<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActQuickbooks\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 * The Profile model repository
 * 
 * @Extender\Mixin
 */
class Profile extends \XLite\Model\Repo\Profile
{
    public const SEARCH_QUICKBOOKS_CUSTOMERS = 'quickbooks_customers';
    
    /**
     * prepareCndQuickbooksCustomers
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder QueryBuilder instance
     * @param mixed                      $value        Searchable value
     *
     * @return void
     */
    protected function prepareCndQuickbooksCustomers(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $queryBuilder->innerJoin(
            'Qualiteam\SkinActQuickbooks\Model\QuickbooksCustomers',
            'qc',
            'WITH',
            'qc.profile_id = p.profile_id'
        );
    }
}