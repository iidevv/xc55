<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\View\Menu\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * Left menu widget
 *
 * @Extender\Mixin
 */
class LeftMenu extends \XLite\View\Menu\Admin\LeftMenu
{
    /**
     * Returns the list of related targets
     *
     * @param string $target Target name
     *
     * @return array
     */
    public function getRelatedTargets($target)
    {
        $targets = parent::getRelatedTargets($target);

        if ('profile_list' == $target) {
            $targets[] = 'x_payments_user_subscription';
        }

        return $targets;
    }

    /**
     * Define items
     *
     * @return array
     */
    protected function defineItems()
    {
        $list = parent::defineItems();

        if (isset($list['sales'])) {
            $list['sales'][static::ITEM_CHILDREN]['x_payments_subscription'] = [
                static::ITEM_TITLE  => static::t('Subscriptions'),
                static::ITEM_TARGET => 'x_payments_subscription',
                static::ITEM_WEIGHT => 2000,
            ];
        }

        return $list;
    }
}
