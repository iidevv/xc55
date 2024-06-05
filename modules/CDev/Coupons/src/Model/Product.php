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
abstract class Product extends \XLite\Model\Product
{
    /**
     * Coupon products
     *
     * @var   \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany (targetEntity="CDev\Coupons\Model\CouponProduct", mappedBy="product")
     */
    protected $couponProducts;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = [])
    {
        $this->couponProducts    = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Add coupon products
     *
     * @param \CDev\Coupons\Model\CouponProduct $couponProduct
     * @return Product
     */
    public function addCouponProducts(\CDev\Coupons\Model\CouponProduct $couponProduct)
    {
        $this->couponProducts[] = $couponProduct;
        return $this;
    }

    /**
     * Get coupon products
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCouponProducts()
    {
        return $this->couponProducts;
    }

    /**
     * Add coupons by their ids
     *
     * @param $couponIds
     */
    public function addSpecificProductCoupons($couponIds)
    {
        foreach ($this->getCouponProducts() as $couponProduct) {
            $couponId = $couponProduct->getCoupon()->getId();
            if (in_array($couponId, $couponIds)) {
                unset($couponIds[array_search($couponId, $couponIds)]);
            }
        }

        foreach ($couponIds as $couponId) {
            $coupon = \XLite\Core\Database::getRepo('CDev\Coupons\Model\Coupon')->find($couponId);

            if ($coupon->getSpecificProducts()) {
                $couponProduct = new \CDev\Coupons\Model\CouponProduct();
                $couponProduct->setProduct($this);
                $couponProduct->setCoupon($coupon);

                \XLite\Core\Database::getEM()->persist($couponProduct);
            }
        }
    }

    /**
     * Remove coupons by their ids
     *
     * @param $couponIds
     */
    public function removeSpecificProductCoupons($couponIds)
    {
        foreach ($this->getCouponProducts() as $couponProduct) {
            $couponId = $couponProduct->getCoupon()->getId();
            if (in_array($couponId, $couponIds)) {
                \XLite\Core\Database::getRepo('CDev\Coupons\Model\CouponProduct')->delete($couponProduct, false);
            }
        }
    }

    /**
     * Replace coupons with coupons with provided ids
     *
     * @param $couponIds
     */
    public function replaceSpecificProductCoupons($couponIds)
    {
        foreach ($this->getCouponProducts() as $couponProduct) {
            $couponId = $couponProduct->getCoupon()->getId();
            if (!in_array($couponId, $couponIds)) {
                \XLite\Core\Database::getRepo('CDev\Coupons\Model\CouponProduct')->delete($couponProduct, false);
            } else {
                unset($couponIds[array_search($couponId, $couponIds)]);
            }
        }

        foreach ($couponIds as $couponId) {
            $coupon = \XLite\Core\Database::getRepo('CDev\Coupons\Model\Coupon')->find($couponId);

            if ($coupon && $coupon->getSpecificProducts()) {
                $couponProduct = new \CDev\Coupons\Model\CouponProduct();
                $couponProduct->setProduct($this);
                $couponProduct->setCoupon($coupon);

                \XLite\Core\Database::getEM()->persist($couponProduct);
            }
        }
    }

    /**
     * Clone
     *
     * @return \XLite\Model\AEntity
     */
    public function cloneEntity()
    {
        $newProduct = parent::cloneEntity();

        if ($this->getCouponProducts()) {
            foreach ($this->getCouponProducts() as $couponProduct) {
                $newCouponProduct = new \CDev\Coupons\Model\CouponProduct();
                $newCouponProduct->setProduct($newProduct);
                $newCouponProduct->setCoupon($couponProduct->getCoupon());

                \XLite\Core\Database::getEM()->persist($newCouponProduct);
            }
        }

        return $newProduct;
    }
}
