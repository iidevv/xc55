<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Returns\View;

use XCart\Extender\Mapping\Extender;

/**
 * Order widget
 * @Extender\Mixin
 */
class Order extends \XLite\View\Order
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/QSL/Returns/order/invoice/style.css';

        return $list;
    }
}
