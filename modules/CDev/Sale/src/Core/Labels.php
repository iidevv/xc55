<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\Core;

/**
 * Class to collect labels for displaying in items list
 */
class Labels extends \XLite\Base\Singleton
{
    /**
     * Runtime labels cache
     *
     * @var array
     */
    protected static $labels = [];

    /**
     * Add label
     *
     * @param \XLite\Model\Product $product Product object
     * @param array                $label   Label
     *
     * @return void
     */
    public static function addLabel(\XLite\Model\Product $product, $label)
    {
        static::$labels[$product->getProductId()] = $label;
    }

    /**
     * Unset label
     *
     * @param \XLite\Model\Product $product Product
     *
     * @return void
     */
    public static function unsetLabel(\XLite\Model\Product $product)
    {
        if (isset(static::$labels[$product->getProductId()])) {
            unset(static::$labels[$product->getProductId()]);
        }
    }

    /**
     * Get registered label for product
     *
     * @param \XLite\Model\Product $product Product object
     *
     * @return array
     */
    public static function getLabel(\XLite\Model\Product $product)
    {
        return !empty(static::$labels[$product->getProductId()])
            ? static::$labels[$product->getProductId()]
            : [];
    }
}
