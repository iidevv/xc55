<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ColorSwatches\View\Form\Product\Modify;

/**
 * Attributes properties
 */
class ColorSwatchesSettings extends \XLite\View\Form\Product\Modify\Base\Single
{
    /**
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'update_color_swatches_settings';
    }
}
