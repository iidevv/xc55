<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\Logic\Export\Step;

use XCart\Extender\Mapping\Extender;

/**
 * Export Orders step.
 * @Extender\Mixin
 */
abstract class Orders extends \XLite\Logic\Export\Step\Orders
{
    /**
     * Define columns.
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        $columns['earnedPoints']   = [];
        $columns['redeemedPoints'] = [];

        return $columns;
    }

    /**
     * Get column value for 'earnedPoints' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getEarnedPointsColumnValue(array $dataset, $name, $i)
    {
        return $this->getColumnValueByName($dataset['model'], 'rewardPoints');
    }

    /**
     * Get column value for 'redeemedPoints' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getRedeemedPointsColumnValue(array $dataset, $name, $i)
    {
        return $this->getColumnValueByName($dataset['model'], 'redeemedPoints');
    }
}
