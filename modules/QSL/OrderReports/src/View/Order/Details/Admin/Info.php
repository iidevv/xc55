<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OrderReports\View\Order\Details\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * Order info
 * @Extender\Mixin
 */
class Info extends \XLite\View\Order\Details\Admin\Info
{
    /**
     * Get order formatted creation date
     *
     * @return boolean
     */
    protected function getMobileOrder()
    {
        return $this->getOrder()->getMobileOrder();
    }
}
