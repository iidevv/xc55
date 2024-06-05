<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button;

/**
 * Place order
 */
class PlaceOrder extends \XLite\View\Button\Submit
{
    /**
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'button/js/place_order.js';

        return $list;
    }

    /**
     * Get default label
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        $cart = $this->getCart();

        $value = $cart->getFirstOpenPaymentTransaction()
            ? $cart->getFirstOpenPaymentTransaction()->getValue()
            : $cart->getTotal();

        return static::t(
            'Place order X',
            [
                'total' => $this->formatPrice(
                    $value,
                    $cart->getCurrency(),
                    true
                ),
            ]
        );
    }

    /**
     * Get default style
     *
     * @return string
     */
    protected function getDefaultStyle()
    {
        return trim(parent::getDefaultStyle() . ' regular-main-button place-order');
    }
}
