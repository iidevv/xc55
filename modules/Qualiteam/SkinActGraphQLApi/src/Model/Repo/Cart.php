<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\Model\Repo;



use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin 
 * [t-converted]

 */

class Cart extends \XLite\Model\Repo\Cart
{
    const P_API_UNIQUE_ID = 'apiCartUniqueId';

    /**
     * Check if cart token is unique
     *
     * @param string $token Cart token
     *
     * @return boolean
     */
    public function isCartTokenExists($token)
    {
        $queryBuilder = $this->createQueryBuilder('c')
            ->select('COUNT(c.order_id)')
            ->andWhere('c.apiCartUniqueId = :token')
            ->setParameter('token', $token);

        return ((int) $queryBuilder->getSingleScalarResult()) == 0;
    }

    /**
     * Find cart by cart token
     *
     * @param $token
     *
     * @return mixed|object
     */
    public function findOneByCartToken($token)
    {
        $cnd                            = new \XLite\Core\CommonCell();
        $cnd->{static::P_API_UNIQUE_ID} = $token;

        $return = $this->search($cnd);

        return !empty($return) ? $return[0] : null;
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Parent category id
     *
     * @return void
     */
    protected function prepareCndApiCartUniqueId(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $queryBuilder->andWhere('c.apiCartUniqueId = :token')
            ->setParameter('token', $value);
    }
}