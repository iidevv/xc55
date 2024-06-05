<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\FeaturedProducts\View\Tabs;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class Category extends \XLite\View\Tabs\Category
{
    /**
     * Returns the list of targets where this widget is available
     *
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'featured_products';

        return $list;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        $list = parent::defineTabs();
        if (\XLite\Core\Request::getInstance()->id) {
            $list['featured_products'] = [
                'weight'   => 400,
                'title'    => static::t('Featured products'),
                'template' => 'modules/CDev/FeaturedProducts/featured_products.twig',
            ];
        }

        return $list;
    }
}
