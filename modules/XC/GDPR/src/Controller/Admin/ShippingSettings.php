<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GDPR\Controller\Admin;

use XCart\Extender\Mapping\Extender;
use XC\GDPR\Core\Activity\Shipping;

/**
 * @Extender\Mixin
 */
class ShippingSettings extends \XLite\Controller\Admin\ShippingSettings
{
    protected function doActionSwitch()
    {
        parent::doActionSwitch();

        $this->updateMethodActivity();
    }

    /**
     * @throws \Exception
     */
    protected function updateMethodActivity()
    {
        if ($method = $this->getMethod()) {
            Shipping::update($method);
            \XLite\Core\Database::getEM()->flush();
        }
    }
}
