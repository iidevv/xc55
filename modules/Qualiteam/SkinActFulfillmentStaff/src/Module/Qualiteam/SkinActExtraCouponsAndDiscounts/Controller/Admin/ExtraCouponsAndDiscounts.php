<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFulfillmentStaff\Module\Qualiteam\SkinActExtraCouponsAndDiscounts\Controller\Admin;

use Qualiteam\SkinActFulfillmentStaff\Core\Core;
use XCart\Extender\Mapping\Extender as Extender;
use XLite\Core\Auth;

/**
 * @Extender\Mixin
 * @Extender\Depend("Qualiteam\SkinActExtraCouponsAndDiscounts")
 */
class ExtraCouponsAndDiscounts extends \Qualiteam\SkinActExtraCouponsAndDiscounts\Controller\Admin\ExtraCouponsAndDiscounts
{
    public function checkACL()
    {
        return parent::checkACL()
            || Core::checkAnyStaffPermission();
    }
}