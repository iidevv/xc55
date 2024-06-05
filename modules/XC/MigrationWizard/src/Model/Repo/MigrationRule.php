<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Model\Repo;

/**
 * Migration Rule Repository
 */
class MigrationRule extends \XLite\Model\Repo\Base\I18n
{
    /**
     * Get all rules for the given requirement
     *
     * @return array(\XC\MigrationWizard\Model\MigrationRule, ...)
     */
    public function getRequirementRules($requirement)
    {
        $queryBuilder = $this->createQueryBuilder('r');

        $namespace = addslashes($requirement->getNamespaceName());

        $query = $queryBuilder->where('r.isSystem = :isSystem')
                ->andWhere('r.logic LIKE :logic')
                ->setParameter('isSystem', false)
                ->setParameter('logic', "%\\{$namespace}\\\\%")
                ->getQuery();

        return $query->getResult();
    }
}
