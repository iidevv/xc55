<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FreeShipping\View\FormField\Inline\Input\Checkbox\Switcher;

use XCart\Extender\Mapping\Extender;

/**
 * Switcher for enabled property
 * @Extender\Mixin
 */
class ShippingMethodEnabled extends \XLite\View\FormField\Inline\Input\Checkbox\Switcher\ShippingMethodEnabled
{
    protected function isDisabled()
    {
        /** @var \XLite\Model\Shipping\Method $shippingMethod */
        $shippingMethod = $this->getEntity();

        return parent::isDisabled()
            || $shippingMethod->getFree()
            || $shippingMethod->isFixedFee();
    }
}
