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
abstract class Membership extends \XLite\Model\Membership
{
    /**
     * Sale discounts
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany (targetEntity="CDev\Sale\Model\SaleDiscount", mappedBy="memberships")
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
}
