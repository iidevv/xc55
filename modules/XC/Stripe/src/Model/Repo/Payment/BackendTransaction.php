<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Stripe\Model\Repo\Payment;

use XCart\Extender\Mapping\Extender;

/**
 * Payment backend transaction repository
 * @Extender\Mixin
 */
class BackendTransaction extends \XLite\Model\Repo\Payment\BackendTransaction
{
    /**
     * Find transaction by data cell
     *
     * @param string $name  Name
     * @param string $value Value
     *
     * @return \XLite\Model\Payment\Transaction
     */
    public function scFindOneByCell($name, $value)
    {
        return $this->scDefineFindOneByCellQuery($name, $value)->getSingleResult();
    }

    /**
     * Define query for scFindOneByCell() method
     *
     * @param string $name  Name
     * @param string $value Value
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function scDefineFindOneByCellQuery($name, $value)
    {
        $qb = parent::createQueryBuilder('t');

        return $qb
            ->linkInner('t.data', 'dataCell')
            ->andWhere('dataCell.name = :name AND dataCell.value = :value')
            ->setParameter('name', $name)
            ->setParameter('value', $value)
            ->setMaxResults(1);
    }
}
