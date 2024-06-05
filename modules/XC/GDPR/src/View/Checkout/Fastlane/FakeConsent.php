<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GDPR\View\Checkout\Fastlane;

use XCart\Extender\Mapping\ListChild;
use XLite\Core\Auth;

/**
 * FakeConsent
 *
 * @ListChild (list="checkout_fastlane.sections.payment.after", weight="2000")
 */
class FakeConsent extends \XLite\View\AView
{
    protected function getDefaultTemplate()
    {
        return 'modules/XC/GDPR/checkout/fastlane/fake_consent.twig';
    }

    /**
     * @return bool
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && !Auth::getInstance()->isUserGdprConsent();
    }

    public function getJSFiles()
    {
        return array_merge(parent::getJSFiles(), [
            'modules/XC/GDPR/checkout/fastlane/fake_consent.js'
        ]);
    }
}
