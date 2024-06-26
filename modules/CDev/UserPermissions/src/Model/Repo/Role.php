<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\UserPermissions\Model\Repo;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Role extends \XLite\Model\Repo\Role
{
    /**
     * Get permanent role
     *
     * @return \XLite\Model\Role
     */
    public function getPermanentRole()
    {
        return $this->defineGetPermanentRoleQuery()->getSingleResult();
    }

    /**
     * Define query for getPermanentRole() method
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineGetPermanentRoleQuery()
    {
        return $this->createQueryBuilder('r')
            ->innerJoin('r.permissions', 'p')
            ->andWhere('r.enabled = :enabled AND p.code = :root')
            ->setParameter('enabled', true)
            ->setParameter('root', \XLite\Model\Role\Permission::ROOT_ACCESS)
            ->setMaxResults(1);
    }
}
