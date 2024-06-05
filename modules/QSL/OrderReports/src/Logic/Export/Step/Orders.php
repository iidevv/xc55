<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OrderReports\Logic\Export\Step;

use XCart\Extender\Mapping\Extender;

/**
 * Orders
 * @Extender\Mixin
 */
class Orders extends \XLite\Logic\Export\Step\Orders
{
    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        $columns['mobileOrder'] = [];

        return $columns;
    }

    /**
     * Get column value for 'mobile_order' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getMobileOrderColumnValue(array $dataset, $name, $i)
    {
        $res = [];

        $res[] = $dataset['model']->getMobileOrder();

        return $res;
    }
}
