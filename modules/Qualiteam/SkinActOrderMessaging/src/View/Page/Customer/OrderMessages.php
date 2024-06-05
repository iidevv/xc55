<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActOrderMessaging\View\Page\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Layout
 * @Extender\Mixin
 */
class OrderMessages extends \XC\VendorMessages\View\Page\Customer\OrderMessages
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/Qualiteam/SkinActOrderMessaging/order_messages/body.twig';
    }
}