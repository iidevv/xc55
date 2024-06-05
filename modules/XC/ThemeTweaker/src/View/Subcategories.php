<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\View;

use XCart\Extender\Mapping\Extender;
use XC\ThemeTweaker;

/**
 * @Extender\Mixin
 * @Extender\Depend ("XC\ThemeTweaker")
 */
abstract class Subcategories extends \XLite\View\Subcategories implements ThemeTweaker\View\LayoutBlockInterface
{
    use ThemeTweaker\View\LayoutBlockTrait;

    /**
     * @return string
     */
    protected function getDefaultDisplayName()
    {
        return static::t('Subcategories');
    }

    /**
     * @return string
     */
    protected function getDefaultLayoutSettingsLink(): string
    {
        $params = [
            'target' => 'categories',
        ];

        $category = $this->getCategory();
        if ($category && !$category->isRootCategory()) {
            $params['id'] = $category->getCategoryId();
        }

        return json_encode($params);
    }

    /**
     * Get current widget type parameter
     *
     * @return boolean
     */
    protected function getWidgetType()
    {
        return $this->getDisplayGroup() === static::DISPLAY_GROUP_SIDEBAR
            ? static::WIDGET_TYPE_SIDEBAR
            : static::WIDGET_TYPE_CENTER;
    }
}
