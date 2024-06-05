<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCreateOrder\Logic\Order\Modifier;

use XCart\Extender\Mapping\Extender;


/**
 * @Extender\Mixin
 */
class Shipping extends \XLite\Logic\Order\Modifier\Shipping
{
    protected function isShippable()
    {
        // order with no orig profile
        if ($this->getOrder()
            && !$this->getOrder()->getOrigProfile()
            && $this->getOrder()->getManuallyCreated()
        ) {
            return true;
        }

        // order with orig profile but with no items
        if ($this->getOrder()
            && $this->getOrder()->getOrigProfile()
            && $this->getOrder()->getManuallyCreated()
            && $this->getOrder()->countItems() === 0
        ) {
            return true;
        }


        return parent::isShippable();
    }

}