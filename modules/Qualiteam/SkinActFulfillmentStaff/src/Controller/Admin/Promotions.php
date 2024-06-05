<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFulfillmentStaff\Controller\Admin;

use Qualiteam\SkinActFulfillmentStaff\Core\Core;
use XCart\Extender\Mapping\Extender as Extender;

/**
 * @Extender\Mixin
 */
class Promotions extends \XLite\Controller\Admin\Promotions
{
    public function checkACL()
    {
        return parent::checkACL()
            || Core::checkAnyStaffPermission();
    }

    protected function checkPageACL($page)
    {
        return parent::checkPageACL($page)
            || Core::checkAnyStaffPermission();
    }
}