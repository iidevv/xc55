<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\VendorMessages\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 * Order repository
 * @Extender\Mixin
 */
class Order extends \XLite\Model\Repo\Order
{
    /**
     * Allowable search params
     */
    public const SEARCH_MESSAGES = 'messages';

    /**
     * Prepare certain search condition
     *
     * @param \XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder Query builder to prepare
     * @param integer                                 $value        Condition data
     *
     * @return void
     */
    protected function prepareCndMessages(\XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder, $value)
    {
        if (!empty($value)) {
            switch ($value) {
                case 'U':
                    if (
                        \XC\VendorMessages\Main::isWarehouse()
                        && \XC\VendorMessages\Main::isVendorAllowedToCommunicate()
                    ) {
                        $queryBuilder->linkLeft('o.conversation', 'pconv')
                            ->linkLeft('o.children', 'children')
                            ->linkLeft('children.conversation', 'cconv')
                            ->linkLeft('pconv.messages', 'pmessages')
                            ->linkLeft('cconv.messages', 'cmessages')
                            ->linkLeft('pmessages.readers', 'r0', \Doctrine\ORM\Query\Expr\Join::WITH, 'r0.reader = :reader')
                            ->linkLeft('pmessages.readers', 'r1')
                            ->linkLeft('cmessages.readers', 'r2', \Doctrine\ORM\Query\Expr\Join::WITH, 'r2.reader = :reader')
                            ->linkLeft('cmessages.readers', 'r3')
                            ->andWhere(
                                $queryBuilder->expr()->orX(
                                    $queryBuilder->expr()->andX(
                                        'pmessages.id IS NOT NULL',
                                        'children.order_id IS NULL'
                                    ),
                                    'cmessages.id IS NOT NULL'
                                )
                            )
                            ->andHaving($queryBuilder->expr()->andX(
                                'COUNT(r1.id) != SUM(IFELSE(r0.id IS NULL, 0, 1)) OR COUNT(r1.id) = 0',
                                'COUNT(r3.id) != SUM(IFELSE(r2.id IS NULL, 0, 1)) OR COUNT(r3.id) = 0'
                            ))
                            ->setParameter('reader', \XLite\Core\Auth::getInstance()->getProfile());
                    } else {
                        $queryBuilder->linkInner('o.conversation', 'conv')
                            ->linkInner('conv.messages')
                            ->linkLeft('messages.readers', 'r0', \Doctrine\ORM\Query\Expr\Join::WITH, 'r0.reader = :reader')
                            ->linkLeft('messages.readers', 'r1')
                            ->andHaving('COUNT(r1.id) != SUM(IFELSE(r0.id IS NULL, 0, 1)) OR COUNT(r1.id) = 0')
                            ->setParameter('reader', \XLite\Core\Auth::getInstance()->getProfile());
                    }

                    break;

                case 'A':
                    if (
                        \XC\VendorMessages\Main::isWarehouse()
                        && \XC\VendorMessages\Main::isVendorAllowedToCommunicate()
                    ) {
                        $queryBuilder->linkLeft('o.conversation', 'pconv')
                            ->linkLeft('o.children', 'children')
                            ->linkLeft('children.conversation', 'cconv')
                            ->linkLeft('pconv.messages', 'pmessages')
                            ->linkLeft('cconv.messages', 'cmessages')
                            ->andWhere('pmessages.id IS NOT NULL OR cmessages.id IS NOT NULL');
                    } else {
                        $queryBuilder->linkInner('o.conversation', 'conv')
                            ->linkInner('conv.messages');
                    }
                    break;
            }
        }
    }
}
