<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Coupons\Module\XC\MailChimp\Logic\UploadingData;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("XC\MailChimp")
 */
class Generator extends \XC\MailChimp\Logic\UploadingData\Generator
{
    protected function getStepsList()
    {
        return array_merge(parent::getStepsList(), [
            'CDev\Coupons\Module\XC\MailChimp\Logic\UploadingData\Step\Coupons'
        ]);
    }
}
