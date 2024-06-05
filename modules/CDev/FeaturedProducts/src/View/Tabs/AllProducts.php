<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\FeaturedProducts\View\Tabs;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Auth;
use XLite\Model\Role\Permission;

/**
 * @Extender\Mixin
 */
abstract class AllProducts extends \XLite\View\Tabs\AllProducts
{
    /**
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();
        if (!\XLite\Core\Request::getInstance()->id) {
            $result[] = 'featured_products';
        }
        return $result;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        $list = parent::defineTabs();

        if (Auth::getInstance()->isPermissionAllowed(Permission::ROOT_ACCESS)) {
            $list['featured_products'] = [
                'weight'     => 150,
                'title'      => static::t('Featured'),
                'url_params' => [
                    'target' => 'featured_products',
                    'page'   => 'products',
                ],
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
        return parent::isVisible() && \XLite\Core\Request::getInstance()->page !== 'front_page';
    }
}
