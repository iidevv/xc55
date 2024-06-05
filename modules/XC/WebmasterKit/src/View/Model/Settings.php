<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\WebmasterKit\View\Model;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Settings extends \XLite\View\Model\Settings
{
    protected function getFormFieldByOption(\XLite\Model\Config $option)
    {
        $cell = parent::getFormFieldByOption($option);

        if ($option->getName() === 'logSQLRegExp') {
            $cell[static::SCHEMA_DEPENDENCY] = [
                static::DEPENDENCY_SHOW => [
                    'logSQL' => [true],
                ],
            ];
        }

        return $cell;
    }
}
