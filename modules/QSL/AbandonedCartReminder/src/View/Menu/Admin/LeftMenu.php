<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\View\Menu\Admin;

use XCart\Extender\Mapping\Extender;
use XLite\Controller\TitleFromController;

/**
 * @Extender\Mixin
 */
abstract class LeftMenu extends \XLite\View\Menu\Admin\LeftMenu
{
    /**
     * @param array $params Handler params OPTIONAL
     */
    public function __construct(array $params = [])
    {
        if (isset($this->relatedTargets['cart_email_stats'])) {
            $this->relatedTargets['cart_email_stats'][] = 'cart_email_stats';
            $this->relatedTargets['cart_email_stats'][] = 'cart_recovery_stats';
            $this->relatedTargets['notifications'][]    = 'cart_reminders';
            $this->relatedTargets['notifications'][]    = 'cart_reminder';
        }

        parent::__construct($params);
    }

    /**
     * @return array
     */
    protected function defineItems()
    {
        $list = parent::defineItems();

        $list['sales'][static::ITEM_CHILDREN]['abandoned_carts'] = [
            static::ITEM_TITLE      => new TitleFromController('abandoned_carts'),
            static::ITEM_TARGET     => 'abandoned_carts',
            static::ITEM_WEIGHT     => 250,
            static::ITEM_PERMISSION => 'manage orders',
        ];
        $list['reports'][static::ITEM_CHILDREN]['cart_email_stats'] = [
            static::ITEM_TITLE      => new TitleFromController('abandoned_carts'),
            static::ITEM_TARGET     => 'cart_email_stats',
            static::ITEM_WEIGHT     => 400,
            static::ITEM_PERMISSION => 'manage orders',
        ];

        return $list;
    }
}
