<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FastLaneCheckout\View\Blocks\PaymentMethods;

use XC\FastLaneCheckout;

/**
 * Checkout Address form
 */
class Selector extends \XLite\View\Checkout\PaymentMethodsList
{
    /**
     * @return string
     */
    public function getDir()
    {
        return FastLaneCheckout\Main::getSkinDir() . 'blocks/payment_methods/';
    }

    /**
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = $this->getDir() . 'selector.js';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . 'selector.twig';
    }

    protected function defineWidgetData()
    {
        return [
            'required' => !$this->isPayedCart(),
            'methodId' => $this->getCart()->getPaymentMethodId(),
        ];
    }

    protected function getWidgetData()
    {
        return json_encode($this->defineWidgetData());
    }
}
