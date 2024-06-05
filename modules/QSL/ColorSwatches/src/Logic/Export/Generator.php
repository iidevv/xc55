<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ColorSwatches\Logic\Export;

use XCart\Extender\Mapping\Extender;

/**
 * Generator
 * @Extender\Mixin
 */
abstract class Generator extends \XLite\Logic\Export\Generator
{
    /**
     * @inheritdoc
     */
    protected function defineSteps()
    {
        $list = parent::defineSteps();
        $list[] = 'QSL\ColorSwatches\Logic\Export\Step\ColorSwatches';

        return $list;
    }
}
