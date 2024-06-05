<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FreeShipping\Logic\Export\Step;

use XCart\Extender\Mapping\Extender;

/**
 * Decorate export product class
 * @Extender\Mixin
 */
class Products extends \XLite\Logic\Export\Step\Products
{
    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        $columns['shipForFree'] = [];
        $columns['freeShipping'] = [];
        $columns['freightFixedFee'] = [];

        return $columns;
    }

    /**
     * Get column value for 'freeShipping' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return boolean
     */
    protected function getShipForFreeColumnValue(array $dataset, $name, $i)
    {
        return $dataset['model']->isShipForFree();
    }

    /**
     * Get column value for 'freeShipping' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getFreeShippingColumnValue(array $dataset, $name, $i)
    {
        return $this->getColumnValueByName($dataset['model'], 'freeShip');
    }

    /**
     * Get column value for 'freeShipping' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getFreightFixedFeeColumnValue(array $dataset, $name, $i)
    {
        return $this->getColumnValueByName($dataset['model'], 'freightFixedFee');
    }
}
