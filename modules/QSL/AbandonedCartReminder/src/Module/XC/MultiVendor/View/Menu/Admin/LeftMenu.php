<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\Module\XC\MultiVendor\View\Menu\Admin;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Auth;

/**
 * @Extender\Mixin
 * @Extender\Depend ({"QSL\AbandonedCartReminder", "XC\MultiVendor"})
 */
abstract class LeftMenu extends \XLite\View\Menu\Admin\LeftMenu
{
    /**
     * @return array
     */
    protected function defineItems()
    {
        $list = parent::defineItems();
        if (static::isRecoveryStatsLeftMenuItemVisible()) {
            unset($list['reports'][static::ITEM_CHILDREN]['cart_email_stats']);
        }

        return $list;
    }

    /**
     * @return bool
     */
    protected static function isRecoveryStatsLeftMenuItemVisible()
    {
        return !Auth::getInstance()->getProfile()->isVendor();
    }
}
