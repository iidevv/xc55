<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Model\Repo;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

/**
 * Abstract repository
 * @Extender\Mixin
 */
class ARepo extends \XLite\Model\Repo\ARepo
{
    // {{{ Import <editor-fold desc="Import" defaultstate="collapsed">

    /**
     * Get identifiers list for specified query builder object
     *
     * @param \Doctrine\ORM\QueryBuilder $qb    Query builder
     * @param string                     $name  Name
     * @param mixed                      $value Value
     *
     * @return void
     */
    protected function addImportCondition(\Doctrine\ORM\QueryBuilder $qb, $name, $value)
    {
        if ($value !== null) {
            parent::addImportCondition($qb, $name, $value);
        } else {
            // Processing NULL values
            $metadata = $this->getClassMetadata();

            if (
                in_array(
                    $metadata->associationMappings[$name]['type'],
                    [
                        ClassMetadataInfo::ONE_TO_ONE,
                    ]
                )
                && property_exists($metadata->name, $name)
            ) {
                $qb->andWhere(
                    $qb->expr()->isNull($qb->getMainAlias() . '.' . $name)
                );
            }
        }
    }

    // }}} </editor-fold>
}
