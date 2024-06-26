<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\Model\Repo\Payment;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Method extends \XLite\Model\Repo\Payment\Method
{
    public const P_EXCLUDED_SERVICE_NAMES = 'excludedServiceNames';

    /**
     * Find payment methods by specified type for dialog 'Add payment method'
     *
     * @param string $type Payment method type
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function findForAdditionByType($type)
    {
        $result = [];
        $methods = parent::findForAdditionByType($type);
        foreach ($methods as $m) {
            $result[] = $m[0];
        }

        return $result;
    }

    /**
     * Define query for findAdditionByType()
     *
     * @param \Doctrine\ORM\QueryBuilder $qb Query builder
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function addOrderByForAdditionByTypeQuery($qb)
    {
        $qb->addSelect('LOCATE(:modulePrefix, m.class) module_prefix')
            ->addOrderBy('module_prefix', 'desc')
            ->setParameter('modulePrefix', 'CDev\\Paypal');

        return parent::addOrderByForAdditionByTypeQuery($qb);
    }

    /**
     * Prepare certain search condition for moduleEnabled flag
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param boolean                    $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag
     *
     * @return void
     */
    protected function prepareCndModuleEnabled(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        parent::prepareCndModuleEnabled($queryBuilder, $value, $countOnly);

        $queryBuilder->andWhere($this->getMainAlias($queryBuilder) . '.service_name != :paypalCreditMethod')
            ->setParameter('paypalCreditMethod', \CDev\Paypal\Main::PP_METHOD_PC);
    }

    /**
     * Prepare certain search condition for moduleEnabled flag
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param boolean                    $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag
     *
     * @return void
     */
    protected function prepareCndExcludedServiceNames(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        $excludedServiceNames = is_array($value) ? $value : [$value];
        $queryBuilder->andWhere($this->getMainAlias($queryBuilder) . '.service_name NOT IN (:excluded_service_names)')
            ->setParameter('excluded_service_names', $excludedServiceNames);
    }
}
