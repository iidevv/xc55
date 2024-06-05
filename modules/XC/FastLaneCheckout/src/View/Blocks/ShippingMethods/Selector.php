<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FastLaneCheckout\View\Blocks\ShippingMethods;

use XC\FastLaneCheckout;

/**
 * Checkout Address form
 */
class Selector extends \XLite\View\ShippingList
{
    /**
     * @return string
     */
    public function getDir()
    {
        return FastLaneCheckout\Main::getSkinDir() . 'blocks/shipping_methods/';
    }

    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = $this->getDir() . 'selector.js';

        return $list;
    }

    public function getCSSFiles()
    {
        return array_merge(parent::getCSSFiles(), [
            'form_field/form_field.css'
        ]);
    }

    protected function getCommonFiles()
    {
        $list = parent::getCommonFiles();

        $list[static::RESOURCE_CSS][] = 'css/chosen/chosen.css';

        return $list;
    }

    protected function getDefaultTemplate()
    {
        return $this->getDir() . 'selector.twig';
    }

    /**
     * @return boolean
     */
    public function shouldReload()
    {
        return \XLite\Model\Shipping::getInstance()->hasOnlineProcessors();
    }
}
