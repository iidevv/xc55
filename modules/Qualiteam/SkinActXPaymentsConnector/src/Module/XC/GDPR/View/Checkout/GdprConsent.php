<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsConnector\Module\XC\GDPR\View\Checkout;

use XCart\Extender\Mapping\Extender;

/**
 * GdprConsent
 *
 * @Extender\Mixin
 * @Extender\Depend({"XC\GDPR"})
 */
class GdprConsent extends \XC\GDPR\View\Checkout\GdprConsent
{
    /**
     * Disable unwanted JS hacks that were required for old Connector module
     *
     * @return bool
     */
    protected function isIframeHackRequired()
    {
        return false;
    }
}
