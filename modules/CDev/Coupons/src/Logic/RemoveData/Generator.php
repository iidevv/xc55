<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Coupons\Logic\RemoveData;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Generator extends \XLite\Logic\RemoveData\Generator
{
    protected function getStepsList()
    {
        $list = parent::getStepsList();
        $list[] = '\CDev\Coupons\Logic\RemoveData\Step\Coupons';

        return $list;
    }
}
