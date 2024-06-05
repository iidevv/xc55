<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActBrandCoupon\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender as Extender;
use XLite\Model\Order;

/**
 * @Extender\Mixin
 */
class Coupon extends \CDev\Coupons\Model\Coupon
{
    /**
     * Memberships
     *
     * @var   \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany (targetEntity="QSL\ShopByBrand\Model\Brand", inversedBy="coupons")
     * @ORM\JoinTable (name="brand_coupons",
     *      joinColumns={@ORM\JoinColumn (name="coupon_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn (name="brand_id", referencedColumnName="brand_id", onDelete="CASCADE")}
     * )
     */
    protected $brands;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = [])
    {
        $this->brands = new ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Add brands
     *
     * @param \QSL\ShopByBrand\Model\Brand $brands
     *
     * @return \CDev\Coupons\Model\Coupon
     */
    public function addBrands(\QSL\ShopByBrand\Model\Brand $brands)
    {
        $this->brands[] = $brands;
        return $this;
    }

    /**
     * Get brands
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBrands()
    {
        return $this->brands;
    }

    /**
     * Clear brands
     */
    public function clearBrands(): void
    {
        foreach ($this->getBrands()->getKeys() as $key) {
            $this->getBrands()->remove($key);
        }
    }

    /**
     * Check coupon compatibility
     *
     * @param \XLite\Model\Order|null $order Order
     *
     * @return boolean
     * @throws \CDev\Coupons\Core\CompatibilityException
     */
    public function checkCompatibility(Order $order = null): bool
    {
        if ($order) {
            $this->checkBrands($order);
        }

        return parent::checkCompatibility($order);
    }

    /**
     * @param \XLite\Model\Order $order
     *
     * @return void
     * @throws \CDev\Coupons\Core\CompatibilityException
     */
    protected function checkBrands(Order $order): void
    {
        if ($this->getBrands()->count()
            && !$this->findBrands($order)
        ) {
           $this->showError();
        }
    }

    /**
     * @param \XLite\Model\Order $order
     *
     * @return bool
     */
    protected function findBrands(Order $order): bool
    {
        foreach ($order->getItems() as $item) {
            if (
                $item->getProduct()->getBrand()
                && $this->getBrands()->contains($item->getProduct()->getBrand())
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return void
     * @throws \CDev\Coupons\Core\CompatibilityException
     */
    protected function showError(): void
    {
        $this->throwCompatibilityException(
            '',
            static::t('SkinActBrandCoupon sorry the coupon you entered cannot be applied to the items in your cart'),
        );
    }

    public function isValidForProduct(\XLite\Model\Product $product)
    {
        $result = parent::isValidForProduct($product);

        if (0 < count($this->getBrands())) {
            // Check brand
            $result = $product->getBrand()
                && $this->getBrands()->contains($product->getBrand());
        }

        return $result;
    }
}