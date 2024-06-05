<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActOrderMessaging\View\ItemsList\Messages\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Order extends \XC\VendorMessages\View\ItemsList\Messages\Customer\Order
{

    protected function getPageBodyTemplate()
    {
        return 'modules/Qualiteam/SkinActOrderMessaging/items_list/messages/order/body.twig';
    }
}