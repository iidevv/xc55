<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFixes\Controller\Customer;

use XCart\Extender\Mapping\Extender;


/**
 * @Extender\Mixin
 * @Extender\Depend({"XC\VendorMessages"})
 */
class Messages extends \XC\VendorMessages\Controller\Customer\Messages
{
    protected function addBaseLocation()
    {
        $this->locationPath[] = new \XLite\View\Location\Node\Home();
    }

}