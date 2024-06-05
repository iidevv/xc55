<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Geolocation\View\Tabs;

use XCart\Extender\Mapping\Extender;
use XCart\Extender\Mapping\ListChild;

/**
 * Tabs related to localization
 *
 * @ListChild (list="admin.center", zone="admin", weight="100")
 * @Extender\Mixin
 */
class CartCheckout extends \XLite\View\Tabs\CartCheckout
{
    /**
     * @return array
     */
    protected function defineTabs()
    {
        $tabs = parent::defineTabs();
        $tabs['shipping_settings']['template'] = 'modules/XC/Geolocation/settings/body_with_alert.twig';

        return $tabs;
    }
}
