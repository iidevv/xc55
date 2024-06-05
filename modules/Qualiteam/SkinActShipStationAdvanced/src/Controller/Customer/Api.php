<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActShipStationAdvanced\Controller\Customer;

use XCart\Extender\Mapping\Extender as Extender;

/**
 * @Extender\Mixin
 */
class Api extends \ShipStation\Api\Controller\Customer\Api
{
    protected function checkStorefrontAccessibility()
    {
        return true;
    }
}