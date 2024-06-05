<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GDPR\Controller\Admin;

use XCart\Extender\Mapping\Extender;
use XC\GDPR\Core\Activity\Payment;

/**
 * @Extender\Mixin
 */
class PaymentSettings extends \XLite\Controller\Admin\PaymentSettings
{
    protected function doActionAdd()
    {
        parent::doActionAdd();

        $this->updateMethodActivity();
    }

    /**
     * @throws \Exception
     */
    protected function updateMethodActivity()
    {
        if ($method = $this->getMethod()) {
            Payment::update($method);
            \XLite\Core\Database::getEM()->flush();
        }
    }

    protected function doActionEnable()
    {
        parent::doActionEnable();

        $this->updateMethodActivity();
    }

    protected function dispatchAJAXEnable()
    {
        $this->updateMethodActivity();

        parent::dispatchAJAXEnable();
    }
}
