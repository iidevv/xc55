<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFulfillmentStaff\Module\Qualiteam\SkinActCreateOrder\Controller\Admin;

use Qualiteam\SkinActFulfillmentStaff\Core\Core;
use XCart\Extender\Mapping\Extender as Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("Qualiteam\SkinActCreateOrder")
 */
class CreateOrder extends \Qualiteam\SkinActCreateOrder\Controller\Admin\CreateOrder
{
    public function checkACL()
    {
        return parent::checkACL()
            || Core::checkAnyStaffPermission();
    }
}