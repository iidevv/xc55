<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\View\FormField\Inline\Label;

/**
 * Attribute value
 */
class AttributeValue extends \XLite\View\FormField\Inline\Input\Text
{
    /**
     * Define form field
     *
     * @return string
     */
    protected function defineFieldClass()
    {
        return 'XC\ProductVariants\View\FormField\Label';
    }
}
