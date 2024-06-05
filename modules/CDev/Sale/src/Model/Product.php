<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\Model;

use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Converter;

/**
 * @Extender\Mixin
 */
class Product extends \XLite\Model\Product
{
    /**
     * The "Discount type" field is equal to this constant if it is "Sale price"
     */
    public const SALE_DISCOUNT_TYPE_PRICE   = 'sale_price';

    /**
     * The "Discount type" field is equal to this constant if it is "Percent off"
     */
    public const SALE_DISCOUNT_TYPE_PERCENT = 'sale_percent';

    /**
     * Flag, if the product participates in the sale
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $participateSale = false;

    /**
     * self::SALE_DISCOUNT_TYPE_PRICE   if "sale value" is considered as "Sale price",
     * self::SALE_DISCOUNT_TYPE_PERCENT if "sale value" is considered as "Percent Off".
     *
     * @var string
     *
     * @ORM\Column (type="string", length=32, nullable=false)
     */
    protected $discountType = self::SALE_DISCOUNT_TYPE_PRICE;

    /**
     * "Sale value"
     *
     * @var float
     *
     * @ORM\Column (type="decimal", precision=14, scale=4)
     */
    protected $salePriceValue = 0;

    /**
     * Sale discount products
     *
     * @var   \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany (targetEntity="CDev\Sale\Model\SaleDiscountProduct", mappedBy="product")
     */
    protected $saleDiscountProducts;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = [])
    {
        $this->saleDiscountProducts = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Add sale discounts by their ids
     *
     * @param $discountIds
     */
    public function addSpecificProductSaleDiscounts($discountIds)
    {
        $discountIds = $this->prepareSaleDiscountIdsForActions($discountIds);

        foreach ($this->getSaleDiscountProducts() as $discountProduct) {
            $discountId = $discountProduct->getSaleDiscount()->getId();
            if (in_array($discountId, $discountIds)) {
                unset($discountIds[array_search($discountId, $discountIds)]);
            }
        }

        foreach ($discountIds as $discountId) {
            $saleDiscount = \XLite\Core\Database::getRepo('CDev\Sale\Model\SaleDiscount')->find($discountId);

            if ($saleDiscount->getSpecificProducts()) {
                $saleDiscountProduct = new \CDev\Sale\Model\SaleDiscountProduct();
                $saleDiscountProduct->setProduct($this);
                $saleDiscountProduct->setSaleDiscount($saleDiscount);

                \XLite\Core\Database::getEM()->persist($saleDiscountProduct);
            }
        }
    }

    /**
     * Remove sale discounts by their ids
     *
     * @param $discountIds
     */
    public function removeSpecificProductSaleDiscounts($discountIds)
    {
        $discountIds = $this->prepareSaleDiscountIdsForActions($discountIds);

        foreach ($this->getSaleDiscountProducts() as $discountProduct) {
            $discountId = $discountProduct->getSaleDiscount()->getId();
            if (in_array($discountId, $discountIds)) {
                \XLite\Core\Database::getRepo('CDev\Sale\Model\SaleDiscountProduct')->delete($discountProduct, false);
            }
        }
    }

    /**
     * Replace sale discounts with sale discounts with provided ids
     *
     * @param $discountIds
     */
    public function replaceSpecificProductSaleDiscounts($discountIds)
    {
        $discountIds = $this->prepareSaleDiscountIdsForActions($discountIds);

        foreach ($this->getSaleDiscountProducts() as $discountProduct) {
            $discountId = $discountProduct->getSaleDiscount()->getId();
            if (!in_array($discountId, $discountIds)) {
                \XLite\Core\Database::getRepo('CDev\Sale\Model\SaleDiscountProduct')->delete($discountProduct, false);
            } else {
                unset($discountIds[array_search($discountId, $discountIds)]);
            }
        }

        foreach ($discountIds as $discountId) {
            $saleDiscount = \XLite\Core\Database::getRepo('CDev\Sale\Model\SaleDiscount')->find($discountId);

            if ($saleDiscount && $saleDiscount->getSpecificProducts()) {
                $saleDiscountProduct = new \CDev\Sale\Model\SaleDiscountProduct();
                $saleDiscountProduct->setProduct($this);
                $saleDiscountProduct->setSaleDiscount($saleDiscount);

                \XLite\Core\Database::getEM()->persist($saleDiscountProduct);
            }
        }
    }

    /**
     * Prepare discount ids for add/remove/replace actions
     *
     * @param $discountIds
     * @return mixed
     */
    protected function prepareSaleDiscountIdsForActions($discountIds)
    {
        return $discountIds;
    }

    /**
     * @return array
     */
    public function getApplicableSaleDiscounts()
    {
        $activeDiscounts = \XLite\Core\Database::getRepo('CDev\Sale\Model\SaleDiscount')
            ->findAllActive();

        $result = [];
        /** @var \CDev\Sale\Model\SaleDiscount $discount */
        foreach ($activeDiscounts as $discount) {
            if ($discount->isApplicableForProduct($this)) {
                $result[] = $discount;
            }
        }

        return $result;
    }

    /**
     * Get discount type
     *
     * @return string
     */
    public function getDiscountType()
    {
        return $this->discountType ?: self::SALE_DISCOUNT_TYPE_PRICE;
    }

    /**
     * Set it to display price with discounts to use in quick data
     *
     * @return float
     */
    public function getQuickDataPrice()
    {
        $price = parent::getQuickDataPrice();

        if ($this->getParticipateSale()) {
            if ($this->getDiscountType() === static::SALE_DISCOUNT_TYPE_PERCENT) {
                $price = $price * (1 - $this->getSalePriceValue() / 100);
            } else {
                $price = $this->getSalePriceValue();
            }
        }

        return $price;
    }

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
     * Add sale discount products
     *
     * @param \CDev\Sale\Model\SaleDiscountProduct $saleDiscountProduct
     * @return Product
     */
    public function addSaleDiscountProducts(\CDev\Sale\Model\SaleDiscountProduct $saleDiscountProduct)
    {
        $this->saleDiscountProducts[] = $saleDiscountProduct;
        return $this;
    }

    /**
     * Get sale discount products
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSaleDiscountProducts()
    {
        return $this->saleDiscountProducts;
    }

    /**
     * Set participateSale
     *
     * @param boolean $participateSale
     * @return Product
     */
    public function setParticipateSale($participateSale)
    {
        $this->participateSale = (bool) $participateSale;
        return $this;
    }

    /**
     * Get participateSale
     *
     * @return boolean
     */
    public function getParticipateSale()
    {
        return $this->participateSale;
    }

    /**
     * Set discountType
     *
     * @param string $discountType
     * @return Product
     */
    public function setDiscountType($discountType)
    {
        $this->discountType = (string) $discountType;
        return $this;
    }

    /**
     * Set salePriceValue
     *
     * @param float $salePriceValue
     * @return Product
     */
    public function setSalePriceValue($salePriceValue)
    {
        $this->salePriceValue = Converter::toUnsigned32BitFloat($salePriceValue);
        return $this;
    }

    /**
     * Get salePriceValue
     *
     * @return float
     */
    public function getSalePriceValue()
    {
        return $this->salePriceValue;
    }

    /**
     * Check if product has sales
     *
     * @return bool
     */
    public function hasParticipateSale()
    {
        return $this->getParticipateSale();
    }
}
