<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\Module\XC\ProductVariants\Core;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("XC\ProductVariants")
 */
class Request extends \XLite\Core\Request
{
    /**
     * Get back2stock hash
     *
     * @param \XLite\Model\Product $product Product
     * @param \XC\ProductVariants\Model\ProductVariant $variant Variant
     *
     * @return string
     */
    public function getBackInStockVariantCookie(\XLite\Model\Product $product, \XC\ProductVariants\Model\ProductVariant $variant = null)
    {
        $name = static::COOKIE_B2S_VAR_PREFIX
            . $product->getProductId();

        if ($variant) {
            $name .= 'variant' . $variant->getVariantId();
        }

        return empty($_COOKIE[$name]) ? null : $_COOKIE[$name];
    }

    /**
     * Set back2stock hash
     *
     * @param \QSL\BackInStock\Model\Record $record Record
     *
     * @return boolean
     */
    public function setBackInStockVariantCookie(\QSL\BackInStock\Model\Record $record)
    {
        $name = static::COOKIE_B2S_VAR_PREFIX
            . $record->getProduct()->getProductId();

        if ($variant = $record->getVariant()) {
            $name .= 'variant' . $variant->getVariantId();
        }

        return $this->setCookie($name, $record->getHash());
    }

    /**
     * Get back2stock hash
     *
     * @param \XLite\Model\Product $product Product
     * @param \XC\ProductVariants\Model\ProductVariant $variant Variant
     *
     * @return string
     */
    public function getBackInStockVariantPriceCookie(\XLite\Model\Product $product, \XC\ProductVariants\Model\ProductVariant $variant = null)
    {
        $name = static::COOKIE_B2S_VAR_PREFIX
            . 'price' . $product->getProductId();

        if ($variant) {
            $name .= 'variant' . $variant->getVariantId();
        }

        return empty($_COOKIE[$name]) ? null : $_COOKIE[$name];
    }

    /**
     * Set back2stock hash
     *
     * @param \QSL\BackInStock\Model\RecordPrice $record Record
     *
     * @return boolean
     */
    public function setBackInStockVariantPriceCookie(\QSL\BackInStock\Model\RecordPrice $record)
    {
        $name = static::COOKIE_B2S_VAR_PREFIX
            . 'price' . $record->getProduct()->getProductId();

        if ($variant = $record->getVariant()) {
            $name .= 'variant' . $variant->getVariantId();
        }

        return $this->setCookie($name, $record->getHash());
    }
}
