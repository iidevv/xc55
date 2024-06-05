<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\View;

use XCart\Extender\Mapping\Extender;
use XC\ThemeTweaker;

/**
 * Abstract widget
 * @Extender\Mixin
 */
abstract class TopCategories extends \XLite\View\TopCategories implements ThemeTweaker\View\LayoutBlockInterface
{
    use ThemeTweaker\View\LayoutBlockTrait;

    /**
     * Returns default display mode
     *
     * @return string
     */
    protected function getDisplayMode()
    {
        if ($this->getDisplayGroup() === static::DISPLAY_GROUP_CENTER) {
            return static::DISPLAY_MODE_TREE;
        } else {
            return parent::getDisplayMode();
        }
    }

    /**
     * @return string
     */
    protected function getDefaultLayoutSettingsLink(): string
    {
        $params = [
            'target' => 'categories',
        ];

        return json_encode($params);
    }
}
