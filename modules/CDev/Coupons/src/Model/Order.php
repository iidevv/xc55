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
abstract class Order extends \XLite\Model\Order
{
    /**
     * Used coupons
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="CDev\Coupons\Model\UsedCoupon", mappedBy="order", cascade={"all"})
     */
    protected $usedCoupons;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = [])
    {
        $this->usedCoupons = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Clone order and all related data
     *
     * @return \XLite\Model\Order
     */
    public function cloneEntity()
    {
        $newOrder = parent::cloneEntity();

        foreach ($this->getUsedCoupons() as $usedCoupon) {
            $cloned = $usedCoupon->cloneEntity();
            $cloned->setOrder($newOrder);
            $newOrder->addUsedCoupons($cloned);
            if ($usedCoupon->getCoupon()) {
                $cloned->setCoupon($usedCoupon->getCoupon());
                $usedCoupon->getCoupon()->addUsedCoupons($cloned);
            }
        }

        return $newOrder;
    }

    /**
     * Define fingerprint keys
     *
     * @return array
     */
    protected function defineFingerprintKeys()
    {
        $list = parent::defineFingerprintKeys();
        $list[] = 'coupons';

        return $list;
    }

    /**
     * Get fingerprint by 'items' key
     *
     * @return array
     */
    protected function getFingerprintByCoupons()
    {
        $coupons = [];
        foreach ($this->getUsedCoupons() as $coupon) {
            /** @var \CDev\Coupons\Model\UsedCoupon $coupon */
            if ($coupon->getCoupon()) {
                $coupons[] = $coupon->getCoupon()->getId();
            } else {
                $coupons[] = 'CODE:' . $coupon->getCode();
            }
        }

        return $coupons;
    }

    // {{{ Coupons manipulation

    /**
     * Add coupon
     *
     * @param \CDev\Coupons\Model\Coupon $coupon Coupon
     *
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     *
     * @return void
     */
    public function addCoupon(\CDev\Coupons\Model\Coupon $coupon)
    {
        $usedCoupon = new \CDev\Coupons\Model\UsedCoupon();

        $usedCoupon->setOrder($this);
        $this->addUsedCoupons($usedCoupon);

        $usedCoupon->setCoupon($coupon);
        $coupon->addUsedCoupons($usedCoupon);

        \XLite\Core\Database::getEM()->persist($usedCoupon);
    }

    /**
     * Remove coupon
     *
     * @param \CDev\Coupons\Model\UsedCoupon $usedCoupon Used coupon
     *
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     *
     * @return void
     */
    public function removeUsedCoupon(\CDev\Coupons\Model\UsedCoupon $usedCoupon)
    {
        if ($this->getUsedCoupons()->removeElement($usedCoupon)) {
            \XLite\Core\Database::getEM()->remove($usedCoupon);
        }
    }

    /**
     * Check if coupon already present
     *
     * @param \CDev\Coupons\Model\Coupon $coupon
     *
     * @return boolean
     */
    public function containsCoupon(\CDev\Coupons\Model\Coupon $coupon)
    {
        return array_reduce($this->getUsedCoupons()->toArray(), static function ($carry, $item) use ($coupon) {
            return $carry || ($item->getCoupon() && $item->getCoupon()->getId() === $coupon->getId());
        }, false);
    }

    /**
     * Check if single use coupon present
     *
     * @return boolean
     */
    public function hasSingleUseCoupon()
    {
        return $this->getUsedCoupons()->exists(static function ($key, $item) {
            return $item->getCoupon() && $item->getCoupon()->getSingleUse();
        });
    }

    // }}}

    // {{{ Status processors

    /**
     * Called when an order successfully placed by a client
     *
     * @return void
     */
    public function processSucceed()
    {
        parent::processSucceed();

        /** @var \CDev\Coupons\Model\UsedCoupon $usedCoupon */
        foreach ($this->getUsedCoupons() as $usedCoupon) {
            $usedCoupon->markAsUsed();
        }
    }

    /**
     * A "change status" handler
     *
     * @return void
     */
    protected function processUncheckout()
    {
        parent::processUncheckout();

        /** @var \CDev\Coupons\Model\UsedCoupon $usedCoupon */
        foreach ($this->getUsedCoupons() as $usedCoupon) {
            $usedCoupon->unmarkAsUsed();
        }
    }

    // }}}

    /**
     * Add usedCoupons
     *
     * @param \CDev\Coupons\Model\UsedCoupon $usedCoupons
     * @return Order
     */
    public function addUsedCoupons(\CDev\Coupons\Model\UsedCoupon $usedCoupons)
    {
        $this->usedCoupons[] = $usedCoupons;
        return $this;
    }

    /**
     * Get usedCoupons
     *
     * @return \Doctrine\Common\Collections\Collection|\CDev\Coupons\Model\UsedCoupon[]
     */
    public function getUsedCoupons()
    {
        return $this->usedCoupons;
    }

    /**
     * Get usedCoupons by coupon
     *
     * @param \CDev\Coupons\Model\Coupon $coupon Coupon
     *
     * @return \XLite\Model\OrderItem[]
     */
    public function getValidItemsByCoupon(\CDev\Coupons\Model\Coupon $coupon)
    {
        $items = [];

        foreach ($this->getItems() as $item) {
            if ($coupon->isValidForProduct($item->getProduct())) {
                $items[] = $item;
            }
        }

        return $items;
    }
}
