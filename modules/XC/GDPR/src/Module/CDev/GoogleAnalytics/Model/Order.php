<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GDPR\Module\CDev\GoogleAnalytics\Model;

use XCart\Extender\Mapping\Extender;

/**
 * Class Order
 *
 * @Extender\Mixin
 * @Extender\Depend ("CDev\GoogleAnalytics")
 */
abstract class Order extends \XLite\Model\Order
{
    /**
     * @return bool
     */
    public function shouldRegisterChange()
    {
        return $this->getProfile()
            ? parent::shouldRegisterChange() && !$this->getProfile()->isDefaultCookiesConsent()
            : parent::shouldRegisterChange();
    }
}
