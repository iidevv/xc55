<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\Controller\Admin;

/**
 * Custom JavaScript controller
 */
class CustomJs extends \XC\ThemeTweaker\Controller\Admin\Base\ThemeTweaker
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Customization');
    }
}
