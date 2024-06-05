<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFulfillmentStaff\Module\CDev\FeaturedProducts\View\Menu\Admin;

use Qualiteam\SkinActFulfillmentStaff\Core\Core;
use XCart\Extender\Mapping\Extender as Extender;
use XLite\Core\Auth;

/**
 * @Extender\Mixin
 * @Extender\Depend("CDev\FeaturedProducts")
 */
class LeftMenu extends \XLite\View\Menu\Admin\LeftMenu
{
    protected function defineItems()
    {
        $items = parent::defineItems();

        if (Auth::getInstance()->isPermissionAllowed(Core::FULFILLMENT_STAFF_PRODUCTS_ACCESS)) {
            $items['store_design'][static::ITEM_CHILDREN]['featured_products'] = [
                static::ITEM_TITLE      => static::t('Featured products'),
                static::ITEM_TARGET     => 'featured_products',
                static::ITEM_EXTRA      => ['page' => 'front_page'],
            ];
        }

        if (Auth::getInstance()->isPermissionAllowed(Core::FULFILLMENT_STAFF_PRODUCTS_ACCESS)) {
            $items['store_design'][static::ITEM_CHILDREN]['featured_products'] = $this->addItemPermission($items['store_design'][static::ITEM_CHILDREN]['featured_products'], Core::FULFILLMENT_STAFF_PRODUCTS_ACCESS);
        }

        return $items;
    }
}