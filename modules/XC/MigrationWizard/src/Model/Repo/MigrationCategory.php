<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Model\Repo;

/**
 * Migration Category Repository
 */
class MigrationCategory extends \XLite\Model\Repo\Base\I18n
{
    /**
     * Get all categories and rules for the given requirement
     *
     * @return array(\XC\MigrationWizard\Model\MigrationCategory, ...)
     */
    public function getRequirementCategoryRules($requirement)
    {
        $queryBuilder = $this->createQueryBuilder('c');

        $namespace = addslashes($requirement->getNamespaceName());

        $query = $queryBuilder->leftJoin('c.rules', 'cr')
                ->innerJoin('cr.rule', 'r', 'WITH', 'r.isSystem = :isSystem AND r.logic LIKE :logic')
                ->setParameter('isSystem', false)
                ->setParameter('logic', "%\\{$namespace}\\\\%")
                ->addOrderBy('c.pos', 'ASC')
                ->addOrderBy('cr.pos', 'ASC')
                ->getQuery();

        return $query->getResult();
    }

    /**
     * Get all enabled categories and rules for the given requirement
     *
     * @return array(\XC\MigrationWizard\Model\MigrationCategory, ...)
     */
    public function getEnabledRequirementCategoryAndRules($requirement)
    {
        $queryBuilder = $this->createQueryBuilder('c');

        $namespace = addslashes($requirement->getNamespaceName());

        $query = $queryBuilder->leftJoin('c.rules', 'cr', 'WITH', 'c.enabled = :enabled')
                ->innerJoin('cr.rule', 'r', 'WITH', 'r.isSystem = :isSystem AND r.logic LIKE :logic AND r.enabled = :enabled')
                ->setParameter('enabled', true)
                ->setParameter('isSystem', false)
                ->setParameter('logic', "%\\{$namespace}\\\\%")
                ->addOrderBy('c.pos', 'ASC')
                ->addOrderBy('cr.pos', 'ASC')
                ->getQuery();

        return $query->getResult();
    }
}
