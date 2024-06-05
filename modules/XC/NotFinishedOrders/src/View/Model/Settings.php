<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\NotFinishedOrders\View\Model;

use XCart\Extender\Mapping\Extender;

/**
 * Settings dialog model widget
 * @Extender\Mixin
 */
class Settings extends \XLite\View\Model\Settings
{
    /**
     * Get form field by option
     *
     * @param \XLite\Model\Config $option Option
     *
     * @return array
     */
    protected function getFormFieldByOption(\XLite\Model\Config $option)
    {
        $cell = parent::getFormFieldByOption($option);

        if ($option->getName() === 'nf_order_ttl') {
            $cell[static::SCHEMA_DEPENDENCY] = [
                static::DEPENDENCY_SHOW => [
                    'limit_nf_order_ttl' => [true],
                ],
            ];
        }

        return $cell;
    }
}
