<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FacebookMarketing\Core;

use XCart\Extender\Mapping\Extender;

/**
 * Current session
 * @Extender\Mixin
 */
class Session extends \XLite\Core\Session
{
    public const PIXEL_PARAM_LAST_INITIATED_CART = 'facebook_pixel_last_initiated_cart';

    /**
     * Set Facebook Pixel last initiated cart id and timestamp
     *
     * @param      $cartId
     * @param null $time
     */
    public function setPixelLastInitiatedCart($cartId, $time = null)
    {
        if ($time === null) {
            $time = \XLite\Core\Converter::getInstance()->time();
        }

        $this->{static::PIXEL_PARAM_LAST_INITIATED_CART} = $cartId . '|' . $time;
    }

    /**
     * Return last initiated cart id and timestamp
     *
     * @return array|null
     */
    public function getPixelLastInitiatedCart()
    {
        if ($this->{static::PIXEL_PARAM_LAST_INITIATED_CART}) {
            return explode('|', $this->{static::PIXEL_PARAM_LAST_INITIATED_CART});
        }

        return null;
    }
}
