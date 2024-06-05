<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFulfillmentStaff\Controller\Admin;

use Qualiteam\SkinActFulfillmentStaff\Core\Core;
use XCart\Extender\Mapping\Extender as Extender;
use XLite\Core\Auth;

/**
 * @Extender\Mixin
 */
class Export extends \XLite\Controller\Admin\Export
{
    public function checkACL()
    {
        // need to check root access since it has access to the Staff permissions
        return (!Auth::getInstance()->hasRootAccess() && Core::checkAnyStaffPermission())
            ? false
            : parent::checkACL();
    }
}
