<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFulfillmentStaff\Controller\Admin\ACL;

use Qualiteam\SkinActFulfillmentStaff\Core\Core;
use XCart\Extender\Mapping\Extender as Extender;
use XLite\Core\Auth;

/**
 * @Extender\Mixin
 */
class Catalog extends \XLite\Controller\Admin\ACL\Catalog
{
    public function checkACL()
    {
        return parent::checkACL()
            || Core::checkAnyStaffPermission();
    }
}