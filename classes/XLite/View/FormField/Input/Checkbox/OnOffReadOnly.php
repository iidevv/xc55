<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input\Checkbox;

class OnOffReadOnly extends \XLite\View\FormField\Input\Checkbox\OnOff
{
    /**
     * Returns disabled state
     *
     * @return boolean
     */
    protected function isDisabled()
    {
        return true;
    }
}
