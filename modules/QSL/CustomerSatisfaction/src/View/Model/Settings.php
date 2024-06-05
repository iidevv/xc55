<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CustomerSatisfaction\View\Model;

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

        switch ($option->getName()) {
            case 'cs_payment_status':
                $cell[static::SCHEMA_DEPENDENCY] = [
                    static::DEPENDENCY_SHOW => [
                        'cs_send_email_by' => ['S'],
                    ],
                ];
                break;

            case 'cs_shipping_status':
                $cell[static::SCHEMA_DEPENDENCY] = [
                    static::DEPENDENCY_SHOW => [
                        'cs_send_email_by' => ['T'],
                    ],
                ];
                break;
        }

        return $cell;
    }
}
