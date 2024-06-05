<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\Model;

use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender;
use XLite\View\FormField\Input\PriceOrPercent;
use XLite\View\FormField\Select\AbsoluteOrPercent;

/**
 * @Extender\Mixin
 * @Extender\Depend("XC\ProductVariants")
 */
class ProductVariant extends \XC\ProductVariants\Model\ProductVariant
{
    /**
     * Sale discount type
     *
     * @var string
     *
     * @ORM\Column (type="string", length=32, nullable=false)
     */
    protected $discountType = Product::SALE_DISCOUNT_TYPE_PRICE;

    /**
     * "Sale value"
     *
     * @var float
     *
     * @ORM\Column (type="decimal", precision=14, scale=4)
     */
    protected $salePriceValue = 0;

    /**
     * Default sale flag
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean", options={"default" : "1"})
     */
    protected $defaultSale = true;

    /**
     * Return old net product price (before sale)
     *
     * @return float
     */
    public function getNetPriceBeforeSale()
    {
        return \CDev\Sale\Logic\PriceBeforeSale::getInstance()->apply($this, 'getClearPrice', ['taxable'], 'net');
    }

    /**
     * Return old display product price (before sale)
     *
     * @return float
     */
    public function getDisplayPriceBeforeSale()
    {
        return \CDev\Sale\Logic\PriceBeforeSale::getInstance()->apply($this, 'getNetPriceBeforeSale', ['taxable'], 'display');
    }

    /**
     * Get quick data price
     *
     * @return float
     */
    public function getQuickDataPrice()
    {
        $price = parent::getQuickDataPrice();

        if (!$this->getDefaultSale()) {
            if ($this->getDiscountType() === Product::SALE_DISCOUNT_TYPE_PERCENT) {
                $price = $price * (1 - $this->getSalePriceValue() / 100);
            } else {
                $price = $this->getSalePriceValue();
            }
        } elseif ($this->getProduct()->getParticipateSale()) {
            if ($this->getProduct()->getDiscountType() === Product::SALE_DISCOUNT_TYPE_PERCENT) {
                $price = $price * (1 - $this->getProduct()->getSalePriceValue() / 100);
            } else {
                $price = $this->getProduct()->getSalePriceValue();
            }
        }

        return $price;
    }

    /**
     * @return bool
     */
    public function getDefaultSale()
    {
        return $this->defaultSale;
    }

    /**
     * @param bool $defaultSale
     */
    public function setDefaultSale($defaultSale)
    {
        $this->defaultSale = $defaultSale;
    }

    /**
     * @return string
     */
    public function getDiscountType()
    {
        return $this->discountType ?: Product::SALE_DISCOUNT_TYPE_PRICE;
    }

    /**
     * @param string $saleDiscountType
     */
    public function setDiscountType($saleDiscountType)
    {
        $this->discountType = $saleDiscountType;
    }

    /**
     * @return float
     */
    public function getSalePriceValue()
    {
        return $this->salePriceValue;
    }

    /**
     * @param float $salePriceValue
     */
    public function setSalePriceValue($salePriceValue)
    {
        $this->salePriceValue = $salePriceValue;
    }

    /**
     * Returns sale field data
     *
     * @return array
     */
    public function getSale()
    {
        $value = $this->getDefaultSale() ? '' : $this->getSalePriceValue();
        $type = $this->getDefaultSale()
            ? (
                $this->getProduct() && $this->getProduct()->getParticipateSale()
                    ? $this->getProduct()->getDiscountType()
                    : Product::SALE_DISCOUNT_TYPE_PERCENT
            )
            : $this->getDiscountType();

        $sale = [
            PriceOrPercent::PRICE_VALUE => $value,
            PriceOrPercent::TYPE_VALUE  => $type === Product::SALE_DISCOUNT_TYPE_PERCENT
                ? AbsoluteOrPercent::TYPE_PERCENT
                : AbsoluteOrPercent::TYPE_ABSOLUTE
        ];

        return $sale;
    }

    /**
     * Set Sale
     *
     * @param array $sale
     * @return ProductVariant
     */
    public function setSale($sale)
    {
        $this->setSalePriceValue(
            $sale[PriceOrPercent::PRICE_VALUE] ?? 0
        );

        $this->setDiscountType(
            isset($sale[PriceOrPercent::TYPE_VALUE])
            && $sale[PriceOrPercent::TYPE_VALUE] === AbsoluteOrPercent::TYPE_PERCENT
                ? Product::SALE_DISCOUNT_TYPE_PERCENT
                : Product::SALE_DISCOUNT_TYPE_PRICE
        );

        return $this;
    }
}
