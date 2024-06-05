<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XC\VendorMessages\Module\XC\MultiVendor\Controller\Admin;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Auth;

/**
 * Order page controller
 * @Extender\Mixin
 * @Extender\Depend("XC\MultiVendor")
 */
abstract class Order extends \XLite\Controller\Admin\Order
{
    /**
     * Check if admin has access to orders.
     *
     * @return bool
     */
    protected function hasAccessToOrders()
    {
        return parent::hasAccessToOrders() || Auth::getInstance()->isPermissionAllowed('manage conversations');
    }
}
