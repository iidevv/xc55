<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActMain\Controller\Admin;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Request;

/**
 * @Extender\Mixin
 */
class OrderStatuses extends \XC\CustomOrderStatuses\Controller\Admin\OrderStatuses
{
    public function getItemsListClass()
    {
        if (Request::getInstance()->itemsList === 'Qualiteam\SkinActMain\View\ItemsList\Model\Order\Status\ShippingBar') {
            return Request::getInstance()->itemsList;
        }

        return parent::getItemsListClass();
    }
}
