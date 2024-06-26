<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\View\FormField\Input;

/**
 * Common checkbox
 */
class Checkbox extends \XLite\View\FormField\Input\Checkbox
{
    /**
     * Get default wrapper class
     *
     * @return string
     */
    protected function getDefaultWrapperClass()
    {
        return parent::getDefaultWrapperClass() . ' checkbox';
    }
}
