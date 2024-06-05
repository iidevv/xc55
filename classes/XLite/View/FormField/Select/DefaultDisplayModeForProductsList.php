<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

/**
 * Select "DefaultDisplayModeForProductsList"
 */
class DefaultDisplayModeForProductsList extends \XLite\View\FormField\Select\Regular
{
    /**
     * getDefaultOptions
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $modes = \XLite\View\ItemsList\Product\Customer\ACustomer::getCenterDisplayModes();

        $options = [];

        foreach ($modes as $key => $mode) {
            $options[$key] = static::t($mode);
        }

        return $options;
    }
}
