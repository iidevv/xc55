<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Returns\Model\Order\Status;

use XCart\Extender\Mapping\Extender;

/**
 * Decorated Shipping status model
 *
 * @Extender\Mixin
 * @Extender\Depend ({"XC\CustomOrderStatuses", "QSL\Returns"})
 */
abstract class ShippingWithCustomOrderStatuses extends \XLite\Model\Order\Status\Shipping
{
    /**
     * Check if the status allows customers to request a return.
     *
     * @return boolean
     */
    public function isReturnRequestAllowed()
    {
        return $this->getIsReturnRequestAllowed();
    }
}
