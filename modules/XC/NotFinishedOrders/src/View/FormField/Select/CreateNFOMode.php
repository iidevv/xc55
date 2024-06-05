<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\NotFinishedOrders\View\FormField\Select;

use XC\NotFinishedOrders\Main;

/**
 * Select "How to show out of stock products"
 */
class CreateNFOMode extends \XLite\View\FormField\Select\Regular
{
    /**
     * getDefaultOptions
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            Main::NFO_MODE_ON_FAILURE => static::t('by payment callback (Failed + Cancel)'),
            Main::NFO_MODE_ON_PLACE   => static::t('by pressing Place order button'),
        ];
    }
}
