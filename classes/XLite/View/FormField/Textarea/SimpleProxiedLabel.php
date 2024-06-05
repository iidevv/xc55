<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Textarea;

class SimpleProxiedLabel extends \XLite\View\FormField\Textarea\Simple
{
    public function getValue()
    {
        $value = (string) static::t(parent::getValue());

        return ($value !== parent::getValue()) ? $value : '';
    }

    public function getRows()
    {
        return 5;
    }
}
