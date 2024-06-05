<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\View\FormField\Input\Text;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class DateRange extends \XLite\View\FormField\Input\Text\DateRange
{
    /**
     * Get formatted range
     *
     * @return string
     */
    public static function abcrConvertToString(array $value)
    {
        return (new static())->convertToString($value);
    }
}
