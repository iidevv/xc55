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
class Coupon extends \CDev\Coupons\Model\Coupon
{
    /**
     * Linked abandoned cart.
     *
     * @var \XLite\Model\Order
     *
     * @ORM\ManyToOne  (targetEntity="XLite\Model\Order", inversedBy="createdCoupons")
     * @ORM\JoinColumn (name="abandonedCart", referencedColumnName="order_id", nullable=true, onDelete="SET NULL")
     */
    protected $abandonedCart;

    /**
     * Whether the coupon was created for an abandoned cart.
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean", nullable=false, options={ "default": false })
     */
    protected $abandonedCartCoupon = false;

    /**
     * Links an abandoned cart to the coupon.
     *
     * @param XLite\Model\Order $abandonedCart Cart model
     *
     * @return Coupon
     */
    public function setAbandonedCart(\XLite\Model\Order $abandonedCart = null)
    {
        $this->abandonedCart = $abandonedCart;

        return $this;
    }

    /**
     * Returns the abandoned cart that is linked to the coupon.
     *
     * @return XLite\Model\Order
     */
    public function getAbandonedCart()
    {
        return $this->abandonedCart;
    }

    /**
     * Marks the coupon as generated for an abandoned cart.
     *
     * @return void
     */
    public function markAsAbandonedCartCoupon()
    {
        $this->abandonedCartCoupon = true;
    }

    /**
     * Checks if the coupon was generated for an abandoned cart.
     *
     * @return bool
     */
    public function isAbandonedCartCoupon()
    {
        return $this->abandonedCartCoupon;
    }

    /**
     * @param bool $abandonedCartCoupon
     *
     * @return static
     */
    public function setAbandonedCartCoupon(bool $abandonedCartCoupon): Coupon
    {
        $this->abandonedCartCoupon = $abandonedCartCoupon;

        return $this;
    }
}
