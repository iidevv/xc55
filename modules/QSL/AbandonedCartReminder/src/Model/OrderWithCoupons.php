<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\Model;

use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("CDev\Coupons")
 */
class OrderWithCoupons extends \XLite\Model\Order
{
    /**
     * Coupons created for the cart/order.
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="CDev\Coupons\Model\Coupon", mappedBy="abandonedCart")
     */
    protected $createdCoupons;

    /**
     * Constructor.
     *
     * @param array $data Entity properties OPTIONAL
     *
     * @return \QSL\AbandonedCartReminder\Model\OrderWithCoupons
     */
    public function __construct(array $data = [])
    {
        $this->createdCoupons = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Links a coupon created for the abandoned cart.
     *
     * @param CDev\Coupons\Model\Coupon $createdCoupons Coupon
     *
     * @return OrderWithCoupons
     */
    public function addCreatedCoupons(\CDev\Coupons\Model\Coupon $createdCoupons)
    {
        $this->createdCoupons[] = $createdCoupons;
        return $this;
    }

    /**
     * Returns coupons created for the abandoned cart.
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getCreatedCoupons()
    {
        return $this->createdCoupons;
    }
}
