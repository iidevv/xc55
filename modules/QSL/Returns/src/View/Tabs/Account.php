<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Returns\View\Tabs;

use XCart\Extender\Mapping\Extender;

/**
 * Tabs related to user profile section
 * @Extender\Mixin
 */
class Account extends \XLite\View\Tabs\Account
{
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/QSL/Returns/items_list/order/style.css';

        return $list;
    }
}
