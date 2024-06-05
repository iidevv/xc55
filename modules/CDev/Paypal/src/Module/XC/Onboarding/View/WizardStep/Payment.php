<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\Module\XC\Onboarding\View\WizardStep;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("XC\Onboarding")
 */
class Payment extends \XC\Onboarding\View\WizardStep\Payment
{
    public const PAYPAL_SORT = 10;

    /**
     * @return array
     */
    protected function getOnlineWidgets()
    {
        $widgets = parent::getOnlineWidgets();
        $widgets[self::PAYPAL_SORT] = \CDev\Paypal\View\Onboarding\PaypalCommercePlatform::class;

        return $widgets;
    }
}
