<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GDPR\Module\XC\FacebookMarketing\View\Header;

use XCart\Extender\Mapping\Extender;
use XCart\Extender\Mapping\ListChild;
use XLite\Core\Auth;

/**
 * Facebook pixel header
 *
 * @Extender\Mixin
 * @Extender\Depend ("XC\FacebookMarketing")
 * @ListChild (list="head", zone="customer")
 */
class Pixel extends \XC\FacebookMarketing\View\Header\Pixel
{
    /**
     * Check cookies consent
     *
     * @return string
     */
    protected function isConsentRevoked()
    {
        return Auth::getInstance()->isUserDefaultCookiesConsent();
    }
}
