<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFulfillmentStaff\Core;

use XLite\Core\Auth;
use XLite\Model\Role;

class Core
{
    const FULFILLMENT_STAFF_ACCESS          = 'fulfillment staff';
    const FULFILLMENT_STAFF_PRODUCTS_ACCESS = 'fulfillment staff products';

    public static function checkAnyStaffPermission()
    {
        return Auth::getInstance()->isPermissionAllowed(Core::FULFILLMENT_STAFF_ACCESS);
    }

    public static function isProfileHasStaffAccess()
    {
        $profile = Auth::getInstance()->getProfile();

        /** @var Role $role */
        foreach ($profile->getRoles() as $role) {
            $permissions = $role->getPermissions();

            if ($permissions) {
                foreach ($permissions as $permission) {
                    if ($permission->getCode() === Core::FULFILLMENT_STAFF_ACCESS) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}