<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkin\View;

use XCart\Extender\Mapping\Extender;

/**
 * Minicart widget
 *
 * @Extender\Mixin
 */
class Minicart extends \XLite\View\Minicart
{
    /**
     * Get items container attributes
     *
     * @return array
     */
    protected function getContainerAttributes()
    {
        $attributes = parent::getContainerAttributes();

        $attributes['class'][] = 'header-icon-menu';

        return $attributes;
    }

}
