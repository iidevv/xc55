<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\Core;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Request extends \XLite\Core\Request
{
    /**
     * Back2stock cookie prefix
     */
    public const COOKIE_B2S_VAR_PREFIX = 'back2stock_hash';

    /**
     * Get back2stock hash
     *
     * @param \XLite\Model\Product $product Product
     *
     * @return string
     */
    public function getBackInStockCookie(\XLite\Model\Product $product)
    {
        $name = static::COOKIE_B2S_VAR_PREFIX . $product->getProductId();

        return empty($_COOKIE[$name]) ? null : $_COOKIE[$name];
    }

    /**
     * Set back2stock hash
     *
     * @param \QSL\BackInStock\Model\Record $record Record
     *
     * @return boolean
     */
    public function setBackInStockCookie(\QSL\BackInStock\Model\Record $record)
    {
        $name = static::COOKIE_B2S_VAR_PREFIX . $record->getProduct()->getProductId();

        return $this->setCookie($name, $record->getHash());
    }

    /**
     * Get back2stock hash
     *
     * @param \XLite\Model\Product $product Product
     *
     * @return string
     */
    public function getBackInStockPriceCookie(\XLite\Model\Product $product)
    {
        $name = static::COOKIE_B2S_VAR_PREFIX . 'price' . $product->getProductId();

        return empty($_COOKIE[$name]) ? null : $_COOKIE[$name];
    }

    /**
     * Set back2stock hash
     *
     * @param \QSL\BackInStock\Model\RecordPrice $record Record
     *
     * @return boolean
     */
    public function setBackInStockPriceCookie(\QSL\BackInStock\Model\RecordPrice $record)
    {
        $name = static::COOKIE_B2S_VAR_PREFIX . 'price' . $record->getProduct()->getProductId();

        return $this->setCookie($name, $record->getHash());
    }
}
