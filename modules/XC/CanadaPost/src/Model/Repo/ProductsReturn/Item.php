<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CanadaPost\Model\Repo\ProductsReturn;

/**
 * Products return item repository
 */
class Item extends \XLite\Model\Repo\ARepo
{
    /**
     * Allowable search params
     */
    public const P_RETURN_ID = 'returnId';

    // {{{ Search: prepare conditions

    /**
     * Prepare "products return ID" condition
     *
     * @param \Doctrine\ORM\QueryBuilder $qb    Query builder to prepare
     * @param integer                    $value Products return ID
     *
     * @return void
     */
    protected function prepareCndReturnId(\Doctrine\ORM\QueryBuilder $qb, $value)
    {
        if (!empty($value)) {
            $qb->linkInner('i.return', 'r');
            $qb->andWhere('r.id = :returnId')
                ->setParameter('returnId', $value);
        }
    }

    // }}}
}
