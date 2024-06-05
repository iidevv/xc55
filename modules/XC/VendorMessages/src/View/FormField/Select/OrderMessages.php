<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\VendorMessages\View\FormField\Select;

/**
 * Order messages selector
 */
class OrderMessages extends \XLite\View\FormField\Select\Regular
{
    /**
     * @inheritdoc
     */
    protected function getDefaultOptions()
    {
        return [
            ''  => static::t('All orders'),
            'U' => static::t('Orders with unread messages'),
            'A' => static::t('Orders with any messages'),
        ];
    }
}
