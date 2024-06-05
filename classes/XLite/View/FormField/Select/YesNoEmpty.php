<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

/**
 * Select "  / Yes / No"
 */
class YesNoEmpty extends \XLite\View\FormField\Select\YesNo
{
    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array_merge(
            ['' => ''],
            parent::getDefaultOptions()
        );
    }
}
