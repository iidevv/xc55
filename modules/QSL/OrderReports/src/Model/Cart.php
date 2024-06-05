<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OrderReports\Model;

use XCart\Extender\Mapping\Extender;

/**
 * Cart
 * @Extender\Mixin
 */
abstract class Cart extends \XLite\Model\Cart
{
    /**
     * Mark cart as order
     *
     * @return void
     */
    public function markAsOrder()
    {
        if (\XLite\Core\MobileDetect::getInstance()->isMobile()) {
            $this->setMobileOrder(true);
        }

        parent::markAsOrder();
    }
}
