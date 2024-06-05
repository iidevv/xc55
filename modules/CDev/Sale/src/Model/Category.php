<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\Model;

use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class Category extends \XLite\Model\Category
{
    /**
     * Sale discounts
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany (targetEntity="CDev\Sale\Model\SaleDiscount", mappedBy="categories")
     */
    protected $saleDiscounts;

    /**
     * Add sale discount
     *
     * @param \CDev\Sale\Model\SaleDiscount $saleDiscount
     */
    public function addSaleDiscount(\CDev\Sale\Model\SaleDiscount $saleDiscount)
    {
        $this->saleDiscounts[] = $saleDiscount;
    }

    /**
     * Get sale discount
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSaleDiscounts()
    {
        return $this->saleDiscounts;
    }

    /**
     * Get all sale discounts which applicable to this category
     *
     * @return array
     */
    public function getApplicableSaleDiscounts()
    {
        $activeDiscounts = \XLite\Core\Database::getRepo('CDev\Sale\Model\SaleDiscount')
            ->findAllActive();

        $result = [];
        /** @var \CDev\Sale\Model\SaleDiscount $discount */
        foreach ($activeDiscounts as $discount) {
            if ($discount->isApplicableForCategory($this)) {
                $result[] = $discount;
            }
        }

        return $result;
    }
}
