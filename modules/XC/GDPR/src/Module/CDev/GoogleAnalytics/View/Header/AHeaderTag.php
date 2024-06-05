<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GDPR\Module\CDev\GoogleAnalytics\View\Header;

use XCart\Extender\Mapping\Extender;
use XCart\Extender\Mapping\ListChild;
use XLite\Core\Auth;

/**
 * Header declaration
 *
 * @Extender\Mixin
 * @Extender\Depend ("CDev\GoogleAnalytics")
 * @ListChild (list="head", zone="customer", weight="0")
 * @ListChild (list="head", zone="admin", weight="0")
 */
abstract class AHeaderTag extends \CDev\GoogleAnalytics\View\Header\AHeaderTag
{
    protected function isTrackingDisabled(): bool
    {
        return Auth::getInstance()->isUserDefaultCookiesConsent();
    }
}
