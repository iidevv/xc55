<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkin\View;

use XCart\Extender\Mapping\Extender;

/**
 * Abstract dialog
 * @Extender\Mixin
 */
abstract class Container extends \XLite\View\Container
{
    /**
     * Return link URL
     *
     * @return string
     */
    protected function getDialogLink() : string
    {
        return '';
    }

    /**
     * Return link label
     *
     * @return string
     */
    protected function getDialogLinkTitle() : string
    {
        return static::t('[SkinActSkin] See all');
    }
}
