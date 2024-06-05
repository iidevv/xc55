<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\View;

use XCart\Extender\Mapping\Extender;

/**
 * Invoice widget
 *
 * @Extender\Mixin
 */
class Invoice extends \XLite\View\Invoice
{
    /**
     * Register files from common repository
     *
     * @return array
     */
    public function getCommonFiles()
    {
        $list = parent::getCommonFiles();

        $list[static::RESOURCE_CSS][] = 'modules/CDev/Paypal/order/invoice/style.less';

        return $list;
    }
}
