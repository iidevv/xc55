<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CustomProductTabs\Logic\Export;

use XCart\Extender\Mapping\Extender;

/**
 * Generator
 * @Extender\Mixin
 */
class Generator extends \XLite\Logic\Export\Generator
{
    /**
     * Define steps
     *
     * @return array
     */
    protected function defineSteps()
    {
        return array_merge(
            parent::defineSteps(),
            [
                'XC\CustomProductTabs\Logic\Export\Step\CustomTabs',
                'XC\CustomProductTabs\Logic\Export\Step\GlobalTabs',
            ]
        );
    }
}
