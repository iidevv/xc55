<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace Qualiteam\SkinActCreateOrder\Model;

use XCart\Extender\Mapping\Extender;


/**
 * @Extender\Mixin
 */
class Zone extends \XLite\Model\Zone
{
    public function getZoneWeight($address)
    {
        $controller = \XLite::getController();

        if ($controller instanceof \XLite\Controller\Admin\Order
            && $order = $controller->getOrder()
        ) {
            if ($order->getManuallyCreated()
                && !$order->getOrigProfile()
            ) {
                return 1;
            }
        }

        return parent::getZoneWeight($address);
    }

}