<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\Model;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class OrderItem extends \XLite\Model\OrderItem
{
    /**
     * Returns the item price as it is displayed in the catalog (with applicable taxes).
     *
     * @return float
     */
    public function getAbandonmentReminderPrice()
    {
        return $this->itemNetPrice;
    }
}
