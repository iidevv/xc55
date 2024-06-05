<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Concierge\View\Model\Shipping;

use XCart\Extender\Mapping\Extender;
use XC\Concierge\Core\Mediator;
use XC\Concierge\Core\Track\ShippingMethod;

/**
 * @Extender\Mixin
 */
abstract class Offline extends \XLite\View\Model\Shipping\Offline
{
    protected function postprocessSuccessActionCreate()
    {
        parent::postprocessSuccessActionCreate();

        Mediator::getInstance()->addMessage(
            new ShippingMethod('Add Shipping Method', $this->getModelObject())
        );
    }
}
