<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFixes\Controller\Customer;

use XCart\Extender\Mapping\Extender;


/**
 * @Extender\Mixin
 * @Extender\Depend({"Qualiteam\SkinActChangesToTrackingNumbers"})
 */
class Parcels extends \Qualiteam\SkinActChangesToTrackingNumbers\Controller\Customer\Parcels
{

    protected function addBaseLocation()
    {
        $this->locationPath[] = new \XLite\View\Location\Node\Home();
    }
}