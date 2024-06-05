<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FreeShipping\Controller\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Import extends \XLite\Controller\Admin\Import
{
    protected function doActionProceed()
    {
        if (
            $this->getImporter()
            && $this->getImporter()->getOptions()->offsetExists('displayFreeShippingUpdateNotification')
            && $this->getImporter()->getOptions()->offsetGet('displayFreeShippingUpdateNotification')
        ) {
            $this->getImporter()->getOptions()->displayFreeShippingUpdateNotification = false;
        }

        parent::doActionProceed();
    }
}
