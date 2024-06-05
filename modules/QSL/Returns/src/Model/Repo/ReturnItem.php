<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Returns\Model\Repo;

/**
 * Return item repository
 *
 */
class ReturnItem extends \XLite\Model\Repo\ARepo
{
    /*
     * Allowed search parameters
     */
    public const SEARCH_ORDER_BY     = 'orderBy';
    public const SEARCH_LIMIT        = 'limit';
    public const SEARCH_ORDER_RETURN = 'orderReturn';

    /**
     * Current condition
     *
     * @var \XLite\Core\CommonCell
     */
    protected $currentSearchCnd = null;

    // {{{ Search

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param object                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndOrderReturn(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if ($value) {
            $queryBuilder
                ->andWhere($this->getMainAlias($queryBuilder) . '.orderReturn = :orderReturn')
                ->setParameter('orderReturn', $value);
        }
    }

    // }}}
}
