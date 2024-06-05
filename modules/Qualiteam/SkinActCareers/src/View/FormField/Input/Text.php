<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCareers\View\FormField\Input;


class Text extends \XLite\View\FormField\Input\Text
{
    protected function getDefaultMaxSize()
    {
        return 4294967295;
    }

    protected function assembleValidationRules()
    {
        $rules = parent::assembleValidationRules();

        foreach ($rules as $ind => $rule) {
            if ($rule === 'maxSize[255]') {
                unset($rules[$ind]);
            }
        }

        return $rules;
    }

    protected function getCommonAttributes()
    {
        $attrs = parent::getCommonAttributes();
        unset($attrs['maxlength']);

        return $attrs;
    }

}