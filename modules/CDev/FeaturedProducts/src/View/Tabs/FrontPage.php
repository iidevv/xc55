<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\FeaturedProducts\View\Tabs;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Auth;

/**
 * @Extender\Mixin
 */
abstract class FrontPage extends \XLite\View\Tabs\FrontPage
{
    /**
     * Returns the list of targets where this widget is available
     *
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        $list   = parent::getAllowedTargets();
        $list[] = 'featured_products';

        return $list;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        $list = parent::defineTabs();

        if (Auth::getInstance()->isPermissionAllowed('manage catalog')) {
            $list['featured_products'] = [
                'weight'     => 300,
                'title'      => static::t('Featured products'),
                'url_params' => ['page' => 'front_page'],
                'template'   => 'modules/CDev/FeaturedProducts/featured_products.twig',
            ];
        }

        return $list;
    }

    /**
     * Checks whether the widget is visible, or not
     *
     * @return bool
     */
    public function isVisible()
    {
        return parent::isVisible() && \XLite\Core\Request::getInstance()->page !== 'products';
    }
}
