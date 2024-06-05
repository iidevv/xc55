<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoSocial\View\FormField\Select;

/**
 * Use Custom Open Graph selector
 */
class UseCustomOpenGraph extends \XLite\View\FormField\Select\ASelect
{
    /**
     * Return default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            false => static::t('Autogenerated'),
            true  => static::t('Custom'),
        ];
    }
}