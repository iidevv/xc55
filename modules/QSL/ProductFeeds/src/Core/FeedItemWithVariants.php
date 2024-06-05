<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductFeeds\Core;

use XCart\Extender\Mapping\Extender;

/**
 * Decorated FeedItem class with Variants module enabled.
 *
 * @Extender\Mixin
 * @Extender\Depend ("XC\ProductVariants")
 */
class FeedItemWithVariants extends \QSL\ProductFeeds\Core\FeedItem
{
    /**
     * Product variant associated with the feed item.
     *
     * @var \XC\ProductVariants\Model\ProductVariant
     */
    protected $variant;

    /**
     * Associate a product variant with the feed item.
     *
     * @param \XC\ProductVariants\Model\ProductVariant $variant Variant to associate with the feed item.
     *
     * @return void
     */
    public function setVariant(\XC\ProductVariants\Model\ProductVariant $variant)
    {
        $this->variant = $variant;
    }

    /**
     * Get the product variant associated with the feed item.
     *
     * @return \XC\ProductVariants\Model\ProductVariant|bool
     */
    public function getVariant()
    {
        return $this->hasVariant() ? $this->variant : false;
    }

    /**
     * Check whether there is a product variant associated with the feed item.
     *
     * @return boolean
     */
    public function hasVariant()
    {
        return isset($this->variant);
    }

    /**
     * Get the product/variant display price.
     *
     * @return float
     */
    public function getDisplayPrice()
    {
        return $this->hasVariant()
           ? $this->getVariant()->getDisplayPrice()
           : $this->getProduct()->getDisplayPrice();
    }

    /**
     * Get the product/variant net price.
     *
     * @return float
     */
    public function getNetPrice()
    {
        return $this->hasVariant()
           ? $this->getVariant()->getNetPrice()
           : $this->getProduct()->getNetPrice();
    }

    /**
     * Get weight of the item in specified units.
     *
     * @param string $units Units OPTIONAL
     *
     * @return string
     */
    public function getWeight($units = 'lbs')
    {
        $weight = $this->hasVariant() ? $this->getVariant()->getClearWeight() : $this->getProduct()->getWeight();

        return \XLite\Core\Converter::convertWeightUnits(
            $weight,
            \XLite\Core\Config::getInstance()->Units->weight_unit,
            $units
        );
    }

    /**
     * Get the product/variant SKU.
     *
     * @return string
     */
    public function getSku()
    {
        return $this->hasVariant()
           ? $this->getVariant()->getDisplaySku()
           : $this->getProduct()->getSku();
    }

    /**
     * Get the family SKU (SKU of the product having variants).
     *
     * @return string
     */
    public function getFamilySku()
    {
        return $this->hasVariant() ? $this->getProduct()->getSku() : '';
    }

    /**
     * Get the product/variant Mnf#/Vendor#.
     *
     * @return string
     */
    public function getMnfVendor()
    {
        $v = $this->hasVariant() ? $this->getVariant()->getMnfVendor() : '';

        return $v ?: $this->getProduct()->getMnfVendor();
    }

    /**
     * Get the family Mnf#/Vendro# (Mnf#/Vendor# of the product having variants).
     *
     * @return string
     */
    public function getFamilyMnfVendor()
    {
        return $this->hasVariant() ? $this->getProduct()->getMnfVendor() : '';
    }

    /**
     * Get the product/variant UPC/ISBN.
     *
     * @return string
     */
    public function getUpcIsbn()
    {
        $v = $this->hasVariant() ? $this->getVariant()->getUpcIsbn() : '';

        return $v ?: $this->getProduct()->getUpcIsbn();
    }

    /**
     * Get the family UPC/ISBN (UPC/ISBN of the product having variants).
     *
     * @return string
     */
    public function getFamilyUpcIsbn()
    {
        return $this->hasVariant() ? $this->getProduct()->getUpcIsbn() : '';
    }

    /**
     * Get the family name (name of the product having variants).
     *
     * @return string
     */
    public function getFamilyName()
    {
        return $this->hasVariant() ? $this->getProduct()->getName() : '';
    }

    /**
     * Get the product/variant quantity in stock.
     *
     * @return integer
     */
    public function getAvailableAmount()
    {
        return $this->hasVariant()
           ? $this->getVariant()->getPublicAmount()
           : parent::getAvailableAmount();
    }

    /**
     * Check whether the product/variant is out of stock, or not.
     *
     * @return boolean
     */
    public function isOutOfStock()
    {
        return $this->hasVariant()
           ? $this->getVariant()->isOutOfStock()
           : parent::isOutOfStock();
    }

    /**
     * Get a value of the product/variant attribute.
     *
     * @param \XLite\Model\Attribute $attribute Attribute instance.
     *
     * @return mixed
     */
    public function getAttributeValue(\XLite\Model\Attribute $attribute)
    {
        $value = ($this->hasVariant() && $this->getVariant()->getAttributeValue($attribute))
             ? $this->getVariant()->getAttributeValue($attribute)->asString()
             : null;

        if ($value === null) {
            $value = parent::getAttributeValue($attribute);
        }

        return $value;
    }
}
