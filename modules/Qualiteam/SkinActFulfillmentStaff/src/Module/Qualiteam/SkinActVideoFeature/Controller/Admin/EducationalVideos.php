<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFulfillmentStaff\Module\Qualiteam\SkinActVideoFeature\Controller\Admin;

use Qualiteam\SkinActFulfillmentStaff\Core\Core;
use XCart\Extender\Mapping\Extender as Extender;
use XLite\Core\Auth;

/**
 * @Extender\Mixin
 * @Extender\Depend("Qualiteam\SkinActVideoFeature")
 */
class EducationalVideos extends \Qualiteam\SkinActVideoFeature\Controller\Admin\EducationalVideos
{
    public function checkACL()
    {
        return parent::checkACL()
            || Core::checkAnyStaffPermission();
    }
}