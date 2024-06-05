<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\View;

use XCart\Extender\Mapping\Extender;

/**
 * Extend Order details page widget
 *
 * @Extender\Mixin
 */
class OrderInfo extends \XLite\View\Order\Details\Admin\Info
{
    /**
     * getCSSFiles
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/CDev/Paypal/order/style.css';

        return $list;
    }
}
