<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\FeaturedProducts\View\Menu\Admin;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Auth;
use XLite\Model\Role\Permission;

/**
 * @Extender\Mixin
 */
abstract class LeftMenu extends \XLite\View\Menu\Admin\LeftMenu
{
    /**
     * @return array
     */
    protected function defineItems()
    {
        $items = parent::defineItems();

        if (!Auth::getInstance()->isPermissionAllowed(Permission::ROOT_ACCESS) && !Auth::getInstance()->isPermissionAllowed('manage front page')) {
            $items['store_design'][static::ITEM_CHILDREN]['featured_products'] = [
                static::ITEM_TITLE      => static::t('Featured products'),
                static::ITEM_TARGET     => 'featured_products',
                static::ITEM_EXTRA      => ['page' => 'front_page'],
                static::ITEM_PERMISSION => 'manage catalog',
            ];
        }

        return $items;
    }
}
