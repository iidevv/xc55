<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\View\Tabs;

use XCart\Extender\Mapping\ListChild;

/**
 * @ListChild (list="admin.center", zone="admin", weight="100")
 */
class BackInStock extends \XLite\View\Tabs\ATabs
{
    /**
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        $list   = parent::getAllowedTargets();
        $list[] = 'back_in_stock_records';
        $list[] = 'back_in_stock_record_prices';

        return $list;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        return [
            'back_in_stock_records' => [
                'weight' => 100,
                'title'  => static::t('Back in stock'),
                'widget' => 'QSL\BackInStock\View\ItemsList\Model\Record',
            ],
            'back_in_stock_record_prices' => [
                'weight' => 200,
                'title'  => static::t('Price drop'),
                'widget' => 'QSL\BackInStock\View\ItemsList\Model\RecordPrice',
            ],
        ];
    }
}
