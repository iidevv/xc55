<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\SalesTax\Model\Repo;

class Tax extends \XLite\Model\Repo\Base\I18n
{
    /**
     * Get tax
     *
     * @return \CDev\SalesTax\Model\Tax
     */
    public function getTax()
    {
        $tax = $this->createQueryBuilder()
            ->setMaxResults(1)
            ->getSingleResult();

        if (!$tax) {
            $tax = $this->createTax();
        }

        return $tax;
    }

    /**
     * Find active taxes
     *
     * @return array
     */
    public function findActive()
    {
        $list = $this->defineFindActiveQuery()->getResult();
        if (count($list) == 0 && count($this->findAll()) == 0) {
            $this->createTax();
            $list = $this->defineFindActiveQuery()->getResult();
        }

        return $list;
    }

    /**
     * Define query for findActive() method
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFindActiveQuery()
    {
        return $this->createQueryBuilder()
            ->addSelect('tr')
            ->linkInner('t.rates', 'tr')
            ->andWhere('t.enabled = :true')
            ->setParameter('true', true);
    }

    /**
     * Create tax
     *
     * @return \CDev\SalesTax\Model\Tax
     */
    protected function createTax()
    {
        $tax = new \CDev\SalesTax\Model\Tax();
        $tax->setName('Sales tax');
        $tax->setEnabled(true);
        \XLite\Core\Database::getEM()->persist($tax);

        return $tax;
    }
}
