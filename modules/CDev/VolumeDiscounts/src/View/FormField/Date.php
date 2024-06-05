<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\VolumeDiscounts\View\FormField;

/**
 * Date
 */
class Date extends \XLite\View\FormField\Input\Text\Date
{
    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/CDev/VolumeDiscounts/date.js';

        return $list;
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'modules/CDev/VolumeDiscounts/date.twig';
    }

    /**
     * @return string
     */
    protected function getDir()
    {
        return '';
    }

    /**
     * Assemble validation rules
     *
     * @return array
     */
    protected function assembleValidationRules()
    {
        $rules = parent::assembleValidationRules();
        $rules[] = 'funcCall[validateVolumeDiscountDate]';

        return $rules;
    }
}
