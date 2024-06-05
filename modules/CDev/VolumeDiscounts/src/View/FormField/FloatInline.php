<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\VolumeDiscounts\View\FormField;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class FloatInline extends \XLite\View\FormField\Inline\Input\Text\FloatInput
{
    /**
     * Get formatted value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->formatValue(parent::getValue());
    }
}
