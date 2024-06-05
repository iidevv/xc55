<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Inline\Input\Checkbox\Switcher;

use XLite\View\FormField\Input\Checkbox\OnOff;

/**
 * Switcher for enabled property
 */
class ShippingMethodEnabled extends \XLite\View\FormField\Inline\Input\Checkbox\Switcher\OnOff
{
    protected function isDisabled()
    {
        /** @var \XLite\Model\Shipping\Method $shippingMethod */
        $shippingMethod = $this->getEntity();

        return $shippingMethod->getProcessorObject() && !$shippingMethod->getProcessorObject()->isConfigured();
    }

    /**
     * Get initial field parameters
     *
     * @param array $field Field data
     *
     * @return array
     */
    protected function getFieldParams(array $field)
    {
        $list = parent::getFieldParams($field);

        $list[OnOff::PARAM_ON_LABEL]  = 'Active';
        $list[OnOff::PARAM_OFF_LABEL] = 'Inactive';
        $list[OnOff::PARAM_DISABLED]  = $this->isDisabled();

        return $list;
    }
}
