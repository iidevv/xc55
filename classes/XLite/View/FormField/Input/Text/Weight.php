<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input\Text;

/**
 * Weight
 */
class Weight extends \XLite\View\FormField\Input\Text\FloatInput
{
    /**
     * Get default E
     *
     * @return integer
     */
    protected static function getDefaultE()
    {
        return 4;
    }
}
