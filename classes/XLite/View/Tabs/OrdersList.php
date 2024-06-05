<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Tabs;

use XCart\Extender\Mapping\ListChild;

/**
 * @ListChild (list="admin.center", zone="admin", weight="100")
 */
class OrdersList extends \XLite\View\Tabs\ATabs
{
    /**
     * Returns the list of targets where this widget is available
     *
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        return array_merge(
            parent::getAllowedTargets(),
            [
                'order_list',
            ]
        );
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        return [
            'order_list' => [
                'weight' => 100,
                'title'  => static::t('All orders'),
                'widget' => 'XLite\View\Order\Search\Search'
            ]
        ];
    }
}
