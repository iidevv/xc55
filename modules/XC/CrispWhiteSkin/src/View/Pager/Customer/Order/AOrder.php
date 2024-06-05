<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\View\Pager\Customer\Order;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class AOrder extends \XLite\View\Pager\Customer\Order\AOrder
{
    protected function getPerPageCounts()
    {
        return [1, 3, 6, 12, 24, 36];
    }
}
