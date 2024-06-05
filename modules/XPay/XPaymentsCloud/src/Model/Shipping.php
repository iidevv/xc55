<?php

// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\Model;

use XCart\Extender\Mapping\Extender;
use XLite\Base\IDecorator;

/**
 * @Extender\Mixin
 */
class Shipping extends \XLite\Model\Shipping implements IDecorator
{
    public function shouldAllowLongCalculations()
    {
        return parent::shouldAllowLongCalculations()
            || \XLite\Core\Request::getInstance()->target === 'wallet_shipping';
    }
}
