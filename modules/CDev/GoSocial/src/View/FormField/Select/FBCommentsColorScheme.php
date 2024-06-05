<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoSocial\View\FormField\Select;

/**
 * Facebook Like button layot styles selector
 */
class FBCommentsColorScheme extends \XLite\View\FormField\Select\Regular
{
    /**
     * getDefaultOptions
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            'light' => 'light',
            'dark'  => 'dark',
        ];
    }
}
