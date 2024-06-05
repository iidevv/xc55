<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\Logic\Export;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class Generator extends \XLite\Logic\Export\Generator
{
    protected function defineSteps()
    {
        $return = parent::defineSteps();

        $return[] = 'QSL\BackInStock\Logic\Export\Step\RecordsStock';
        $return[] = 'QSL\BackInStock\Logic\Export\Step\RecordsPrice';

        return $return;
    }
}
