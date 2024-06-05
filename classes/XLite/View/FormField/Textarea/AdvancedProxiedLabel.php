<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Textarea;

/**
 * Textarea with WYSIWYG editor
 */
class AdvancedProxiedLabel extends \XLite\View\FormField\Textarea\Advanced
{
    public function getValue()
    {
        $value = (string) static::t(parent::getValue());

        return ($value !== parent::getValue()) ? $value : '';
    }
}
