<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ColorSwatches\View\Export;

use XCart\Extender\Mapping\Extender;

/**
 * Begin section
 * @Extender\Mixin
 */
class Begin extends \XLite\View\Export\Begin
{
    /**
     * @inheritdoc
     */
    protected function getSections()
    {
        $list = parent::getSections();
        $list['QSL\ColorSwatches\Logic\Export\Step\ColorSwatches'] = 'Color swatches';

        return $list;
    }
}
