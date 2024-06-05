<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Model\Repo;

/**
 * The "product" model repository
 */
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin 
 * [t-converted]

 */

abstract class Profile extends \XLite\Model\Repo\Profile
{
    const SEARCH_SIMPLE_USER_TYPE = 'simpleUserType';
    const USER_TYPE_ANONYMOUS   = 'N';
    const USER_TYPE_STAFF       = 'A';

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Parent category id
     *
     * @return void
     */
    protected function prepareCndSimpleUserType(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $condition = $queryBuilder->expr()->orX();

        if ($value == static::USER_TYPE_STAFF) {
            $condition->add(
                $queryBuilder->expr()->andX(
                    $queryBuilder->getRegisteredCondition(),
                    $queryBuilder->getAdminCondition()
                )
            );

            $queryBuilder->setParameter('anonymous', true);

        } elseif ($value == static::USER_TYPE_ANONYMOUS) {
            $condition->add(
                $queryBuilder->expr()->andX(
                    $queryBuilder->getAnonymousCondition(),
                    $queryBuilder->getCustomerCondition()
                )
            );

            $queryBuilder->setParameter('anonymous', true);

        } else {
            $condition->add(
                $queryBuilder->expr()->andX(
                    $queryBuilder->getCustomerCondition()
                )
            );
        }

        $queryBuilder->setParameter('adminAccessLevel', \XLite\Core\Auth::getInstance()->getAdminAccessLevel());

        if ($condition->count()) {
            $queryBuilder->andWhere($condition);
        }
    }
}
