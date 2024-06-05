<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\View\Menu\Admin;

use XCart\Extender\Mapping\Extender;
use XPay\XPaymentsCloud\Main as XPaymentsHelper;

/**
 * Left menu widget
 *
 * @Extender\Mixin
 */
abstract class LeftMenu extends \XLite\View\Menu\Admin\LeftMenu implements \XLite\Base\IDecorator
{
    const XPAYMENTS_SUBSCRIPTIONS_TARGET = 'xpayments_subscriptions';

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
            $targets[] = 'xpayments_user_subscriptions';
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

        if (
            XPaymentsHelper::isSubscriptionManagementEnabled()
            && isset($list['sales'])
        ) {
            $list['sales'][static::ITEM_CHILDREN][static::XPAYMENTS_SUBSCRIPTIONS_TARGET] = [
                static::ITEM_TITLE  => static::t('Subscriptions list'),
                static::ITEM_TARGET => static::XPAYMENTS_SUBSCRIPTIONS_TARGET,
                static::ITEM_WEIGHT => 2000,
            ];
        }

        return $list;
    }
}
