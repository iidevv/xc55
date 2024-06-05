<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OrderReports\View\Menu\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Before ("XC\MultiVendor")
 */
abstract class LeftMenu extends \XLite\View\Menu\Admin\LeftMenu
{
    /**
     * @param array $params Handler params OPTIONAL
     */
    public function __construct(array $params = [])
    {
        if (!isset($this->relatedTargets['reports'])) {
            $this->relatedTargets['reports'] = [];
        }

        $this->relatedTargets['reports'][] = 'orders_stats';
        $this->relatedTargets['reports'][] = 'top_sellers';
        $this->relatedTargets['reports'][] = 'cart_email_stats';
        $this->relatedTargets['reports'][] = 'cart_recovery_stats';

        parent::__construct($params);
    }

    /**
     * @return array
     */
    protected function defineItems()
    {
        $list = parent::defineItems();

        $list['reports'][static::ITEM_CHILDREN]['order_reports'] = [
            static::ITEM_TITLE      => static::t('Order reports'),
            static::ITEM_TARGET     => 'order_reports',
            static::ITEM_WEIGHT     => 150,
            static::ITEM_PERMISSION => 'view order reports',
        ];

        return $list;
    }
}
