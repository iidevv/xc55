<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CustomerSatisfaction\View\FormField\Select;

/**
 * 'Cache reset mode' selector
 */
class SendEmailBy extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            'S'  => static::t('by payment status'),
            'T'  => static::t('by shipping status'),
        ];
    }
}
