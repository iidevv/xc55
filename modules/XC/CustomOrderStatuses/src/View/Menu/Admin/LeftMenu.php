<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CustomOrderStatuses\View\Menu\Admin;

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
        if (!isset($this->relatedTargets['order_statuses'])) {
            $this->relatedTargets['order_statuses'] = [];
        }

        $this->relatedTargets['order_statuses'][] = 'order_statuses';

        parent::__construct($params);

        $this->addRelatedTarget('order_statuses', 'order_statuses', ['page' => 'shipping'], ['page' => 'payment']);
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
            $list['sales'][static::ITEM_CHILDREN]['order_statuses'] = [
                static::ITEM_TITLE      => new TitleFromController('order_statuses'),
                static::ITEM_TARGET     => 'order_statuses',
                static::ITEM_EXTRA      => ['page' => 'payment'],
                static::ITEM_PERMISSION => 'manage orders',
                static::ITEM_WEIGHT     => 350,
            ];
        }

        return $list;
    }
}
