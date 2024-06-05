<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select\Order;

/**
 * Customer access level selector
 */
class CustomerAccessLevel extends \XLite\View\FormField\Select\ASelect
{
    /**
     * Available options
     */
    public const ACCESS_LEVEL_REGISTERED = 'registered';
    public const ACCESS_LEVEL_ANONYMOUS = 'anonymous';

    /**
     * Return default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            ''                              => static::t('All levels'),
            static::ACCESS_LEVEL_REGISTERED => static::t('Registered (access level)'),
            static::ACCESS_LEVEL_ANONYMOUS  => static::t('Anonymous (access level)'),
        ];
    }
}
