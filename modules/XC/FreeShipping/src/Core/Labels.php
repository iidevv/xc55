<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FreeShipping\Core;

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
     * Get registered label for product
     *
     * @param \XLite\Model\Product $product Product object
     *
     * @return array
     */
    public static function getLabel(\XLite\Model\Product $product)
    {
        if (
            $product->isAvailable()
            && !isset(static::$labels[$product->getProductId()])
        ) {
            static::$labels[$product->getProductId()] = (
                $product->getFreeShip()
                || $product->isShipForFree()
            ) && $product->getShippable()
                ? static::getLabelContent()
                : '';
        }

        return !empty(static::$labels[$product->getProductId()])
            ? static::$labels[$product->getProductId()]
            : [];
    }

    /**
     * Get content of Free shipping label
     *
     * @return array
     */
    protected static function getLabelContent()
    {
        return [
            'blue free-shipping' => \XLite\Core\Translation::getInstance()->translate('FREE'),
        ];
    }
}
