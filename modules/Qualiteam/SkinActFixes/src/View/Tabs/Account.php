<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFixes\View\Tabs;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend({"Qualiteam\SkinActXPaymentsConnector"})
 * @Extender\After({"Qualiteam\SkinActXPaymentsSubscriptions"})
 */
class Account extends \XLite\View\Tabs\Account
{

    protected function defineTabs()
    {
        $tabs = parent::defineTabs();

        unset($tabs['saved_cards']);

        if (isset($tabs['x_payments_subscription'])) {
            $tabs['x_payments_subscription']['title'] .= ' (view only)';
        }

        return $tabs;
    }
}
