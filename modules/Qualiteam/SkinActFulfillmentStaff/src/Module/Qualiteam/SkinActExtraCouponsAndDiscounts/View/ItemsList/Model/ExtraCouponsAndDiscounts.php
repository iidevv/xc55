<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFulfillmentStaff\Module\Qualiteam\SkinActExtraCouponsAndDiscounts\View\ItemsList\Model;

use Qualiteam\SkinActFulfillmentStaff\Core\Core;
use XCart\Extender\Mapping\Extender as Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("Qualiteam\SkinActExtraCouponsAndDiscounts")
 */
class ExtraCouponsAndDiscounts extends \Qualiteam\SkinActExtraCouponsAndDiscounts\View\ItemsList\Model\ExtraCouponsAndDiscounts
{

    protected function checkACL()
    {
        return parent::checkACL()
            || Core::checkAnyStaffPermission();
    }
}