<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Coupons\Model;

use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class Category extends \XLite\Model\Category
{
    /**
     * Coupons
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany (targetEntity="CDev\Coupons\Model\Coupon", mappedBy="categories")
     */
    protected $coupons;

    /**
     * Add coupons
     *
     * @param \CDev\Coupons\Model\Coupon $coupons
     * @return Category
     */
    public function addCoupons(\CDev\Coupons\Model\Coupon $coupons)
    {
        $this->coupons[] = $coupons;
        return $this;
    }

    /**
     * Get coupons
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCoupons()
    {
        return $this->coupons;
    }
}
