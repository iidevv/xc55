<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\VendorMessages\View\Menu\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * Left side menu widget
 *
 * @Extender\Mixin
 * @Extender\After ("XC\VendorMessages")
 * @Extender\Depend ("XC\MultiVendor")
 */
class LeftMenuMultivendor extends \XLite\View\Menu\Admin\LeftMenu
{
    protected function defineItems()
    {
        $items = parent::defineItems();
        if (isset($items['communications'][static::ITEM_CHILDREN]['messages']) && \XLite\Core\Auth::getInstance()->isVendor()) {
            if (\XC\VendorMessages\Main::isVendorAllowedToCommunicate()) {
                $items['communications'][static::ITEM_CHILDREN]['messages'][static::ITEM_PERMISSION] = '[vendor] manage orders';
            } else {
                unset($items['communications'][static::ITEM_CHILDREN]['messages']);
            }
        }

        return $items;
    }
}
