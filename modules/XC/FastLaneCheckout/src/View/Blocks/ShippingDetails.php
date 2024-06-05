<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FastLaneCheckout\View\Blocks;

use XC\FastLaneCheckout;

/**
 * Checkout order notes
 */
class ShippingDetails extends \XLite\View\AView
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . 'template.twig';
    }

    /**
     * @return string
     */
    public function getDir()
    {
        return FastLaneCheckout\Main::getSkinDir() . 'blocks/shipping_details/';
    }

    /**
     * Get JS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = [
            'file'  => $this->getDir() . 'style.less',
            'media' => 'screen',
            'merge' => 'bootstrap/css/bootstrap.less',
        ];

        return $list;
    }

    /**
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = [];

        $list[] = $this->getDir() . 'shipping-details.js';

        return $list;
    }

    // Data accessors

    /**
     * @return string description
     */
    protected function getOrderNotes()
    {
        $cart = $this->getCart();

        return $cart
             ? $cart->getNotes()
             : '';
    }

    /**
     * @return string description
     */
    protected function getShippingMethod()
    {
        $cart = $this->getCart();

        return $cart
             ? $cart->getShippingMethodName()
             : '';
    }
}
