<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FastLaneCheckout\View\Checkout;

use XCart\Extender\Mapping\ListChild;

/**
 * FakeConsent to avoid problems like this https://xcn.myjetbrains.com/youtrack/issue/BUG-6493#focus=Comments-73-41877.0-0
 *
 * @ListChild (list="checkout_fastlane.sections.payment.after", weight="2000")
 */
class FakeToSConsent extends \XLite\View\AView
{
    protected function getDefaultTemplate()
    {
        return 'modules/XC/FastLaneCheckout/sections/fake_tos_consent.twig';
    }

    /**
     * @return bool
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && \XLite\Core\Config::getInstance()->General->terms_conditions_confirm_type == 'Clickwrap';
    }

    public function getJSFiles()
    {
        return array_merge(parent::getJSFiles(), [
            'modules/XC/FastLaneCheckout/sections/fake_tos_consent.js',
        ]);
    }
}
