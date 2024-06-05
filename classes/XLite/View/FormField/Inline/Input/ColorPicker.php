<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Inline\Input;

class ColorPicker extends \XLite\View\FormField\Inline\Base\Single
{
    /**
     * @return string
     */
    protected function defineFieldClass()
    {
        return 'XLite\View\FormField\Input\ColorPicker';
    }

    /**
     * @return bool
     */
    protected function hasSeparateView()
    {
        return false;
    }
}
