<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGoogleProductRatingFeed\View\FormField\Select;

use XLite\Core\Task\Base\Periodic;

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
            Periodic::INT_1_HOUR => static::t('hourly'),
            Periodic::INT_1_DAY  => static::t('daily'),
            Periodic::INT_1_WEEK => static::t('weekly'),
        ];
    }
}