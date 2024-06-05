<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Returns\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Order page controller
 * @Extender\Mixin
 */
class Order extends \XLite\Controller\Customer\Order
{
    public function isActionsEnabled()
    {
        return (bool)\XLite\Core\Config::getInstance()->QSL->Returns->enable_actions;
    }
}
