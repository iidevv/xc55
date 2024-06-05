<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FacebookMarketing\View\FormField\Select;

class RenewalFrequency extends \XLite\View\FormField\Select\ASelect
{
    /**
     * Return default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            \XLite\Core\Task\Base\Periodic::INT_1_HOUR => static::t('hourly'),
            \XLite\Core\Task\Base\Periodic::INT_1_DAY => static::t('daily'),
            \XLite\Core\Task\Base\Periodic::INT_1_WEEK => static::t('weekly'),
        ];
    }
}
