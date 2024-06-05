<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\FeaturedProducts\Module\XC\ThemeTweaker\View\Customer;

use XCart\Extender\Mapping\Extender;
use XC\ThemeTweaker;

/**
 * @Extender\Mixin
 * @Extender\Depend ("XC\ThemeTweaker")
 */
abstract class FeaturedProducts extends \CDev\FeaturedProducts\View\Customer\FeaturedProducts implements ThemeTweaker\View\LayoutBlockInterface
{
    use ThemeTweaker\View\LayoutBlockTrait;

    /**
     * Returns default display mode
     *
     * @return string
     */
    protected function getDisplayMode()
    {
        if ($this->getDisplayGroup() === static::DISPLAY_GROUP_SIDEBAR) {
            return static::DISPLAY_MODE_STHUMB;
        } else {
            return parent::getDisplayMode();
        }
    }

    /**
     * Get current widget type parameter
     *
     * @return boolean
     */
    protected function getWidgetType()
    {
        return $this->getDisplayGroup() === static::DISPLAY_GROUP_SIDEBAR
            ?  static::WIDGET_TYPE_SIDEBAR
            :  static::WIDGET_TYPE_CENTER;
    }

    /**
     * @return string
     */
    protected function getDefaultLayoutSettingsLink(): string
    {
        $params = [
            'target' => 'featured_products',
            'page'   => 'front_page'
        ];

        $category = $this->getCategory();
        if ($category && !$category->isRootCategory()) {
            $params['id'] = $category->getCategoryId();
        }

        return json_encode($params);
    }
}
