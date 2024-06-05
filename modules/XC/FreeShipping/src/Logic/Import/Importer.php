<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FreeShipping\Logic\Import;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Config;

/**
 * @Extender\Mixin
 */
class Importer extends \XLite\Logic\Import\Importer
{
    public function isNextStepAllowed()
    {
        return parent::isNextStepAllowed()
            && !$this->isDisplayFreeShippingUpdateNotice();
    }

    public function isDisplayFreeShippingUpdateNotice()
    {
        return Config::getInstance()->XC->FreeShipping->display_update_import_info
            && $this->getOptions()->offsetExists('displayFreeShippingUpdateNotification')
            && $this->getOptions()->offsetGet('displayFreeShippingUpdateNotification');
    }
}
