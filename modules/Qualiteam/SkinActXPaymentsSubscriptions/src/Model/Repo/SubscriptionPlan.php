<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\Model\Repo;

use Doctrine\ORM\QueryBuilder;
use XLite\Core\CommonCell;
use XLite\Model\Repo\ARepo;

/**
 * Subscription plans repository
 */
class SubscriptionPlan extends ARepo
{
    const SEARCH_ACTIVE = 'active';

    /**
     * Current search condition
     *
     * @var CommonCell
     */
    protected $currentSearchCnd;

    /**
     * Default model alias
     *
     * @var string
     */
    protected $defaultAlias = 'sp';

    /**
     * Prepare certain search condition
     *
     * @param QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndActive(QueryBuilder $queryBuilder, $value)
    {
        $queryBuilder->where('sp.subscription != 0');
    }
}
