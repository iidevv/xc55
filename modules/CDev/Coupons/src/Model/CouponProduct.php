<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Coupons\Model;

use Doctrine\ORM\Mapping as ORM;
use XLite\Model\Product as ProductEntity;

/**
 * Category
 *
 * @ORM\Entity
 * @ORM\Table (name="coupon_products",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint (name="pair", columns={"coupon_id","product_id"})
 *      },
 * )
 */
class CouponProduct extends \XLite\Model\AEntity
{
    /**
     * Primary key
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Relation to a coupon entity
     *
     * @var \CDev\Coupons\Model\Coupon
     *
     * @ORM\ManyToOne  (targetEntity="CDev\Coupons\Model\Coupon", inversedBy="couponProducts")
     * @ORM\JoinColumn (name="coupon_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $coupon;

    /**
     * Relation to a product entity
     *
     * @var ProductEntity
     *
     * @ORM\ManyToOne  (targetEntity="XLite\Model\Product", inversedBy="couponProducts")
     * @ORM\JoinColumn (name="product_id", referencedColumnName="product_id", onDelete="CASCADE")
     */
    protected $product;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Coupon|null
     */
    public function getCoupon(): ?Coupon
    {
        return $this->coupon;
    }

    /**
     * @param Coupon $coupon
     */
    public function setCoupon($coupon): CouponProduct
    {
        $this->coupon = $coupon;

        return $this;
    }

    /**
     * @return ProductEntity
     */
    public function getProduct(): ?ProductEntity
    {
        return $this->product;
    }

    /**
     * @param ProductEntity|null $product
     */
    public function setProduct(?ProductEntity $product): CouponProduct
    {
        $this->product = $product;

        return $this;
    }

    /**
     * @return string
     */
    public function getProductName()
    {
        return $this->getProduct()->getName();
    }

    /**
     * @return float
     */
    public function getProductPrice()
    {
        return $this->getProduct()->getPrice();
    }

    /**
     * @return string
     */
    public function getProductSku()
    {
        return $this->getProduct()->getSku();
    }

    /**
     * @return int
     */
    public function getProductAmount()
    {
        return $this->getProduct()->getAmount();
    }
}
