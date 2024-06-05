<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GDPR\View\Checkout\Fastlane;

use XCart\Extender\Mapping\ListChild;

/**
 * GdprConsent
 *
 * @ListChild (list="checkout_fastlane.sections.place-order.before", weight="50")
 */
class GdprConsent extends \XC\GDPR\View\Checkout\GdprConsent
{
    protected function getDefaultTemplate()
    {
        return 'modules/XC/GDPR/checkout/fastlane/gdpr_consent.twig';
    }

    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        unset($list[0]);
        $list[] = 'modules/XC/GDPR/checkout/fastlane/gdpr_consent.js';

        return $list;
    }
}
