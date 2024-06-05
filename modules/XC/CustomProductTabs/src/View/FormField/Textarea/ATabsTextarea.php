<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CustomProductTabs\View\FormField\Textarea;

/**
 * Abstract custom tabs textarea
 */
abstract class ATabsTextarea extends \XLite\View\FormField\Textarea\Advanced
{
    /**
     * Get default wrapper class
     *
     * @return string
     */
    protected function getDefaultWrapperClass()
    {
        $class = parent::getDefaultWrapperClass();

        return $class . ' textarea-advanced';
    }
}
