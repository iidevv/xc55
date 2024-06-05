<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\Logic\Export\Step;

use XCart\Extender\Mapping\Extender;

/**
 * Export Products step.
 * @Extender\Mixin
 */
abstract class Products extends \XLite\Logic\Export\Step\Products
{
    /**
     * Define columns.
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        $columns['rewardPoints'] = [];

        return $columns;
    }

    /**
     * Get column value for 'rewardPoints' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getRewardPointsColumnValue(array $dataset, $name, $i)
    {
        $product = $dataset['model'];

        return $product->hasDefinedRewardPoints()
            ? $this->getColumnValueByName($product, 'rewardPoints')
            : 'auto';
    }
}
