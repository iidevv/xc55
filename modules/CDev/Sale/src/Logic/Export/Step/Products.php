<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\Logic\Export\Step;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class Products extends \XLite\Logic\Export\Step\Products
{
    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        $columns['sale'] = [];
        $columns['saleDiscounts'] = [];

        return $columns;
    }

    /**
     * Get column value for 'sale' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getSaleColumnValue(array $dataset, $name, $i)
    {
        $result = '';

        if ($dataset['model']->getParticipateSale()) {
            $result = $this->getColumnValueByName($dataset['model'], 'salePriceValue');
            if ($dataset['model']->getDiscountType() == \XLite\Model\Product::SALE_DISCOUNT_TYPE_PERCENT) {
                $result .= '%';
            }
        }

        return $result;
    }

    /**
     * Get column value for 'saleDiscounts' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return array
     */
    protected function getSaleDiscountsColumnValue(array $dataset, $name, $i)
    {
        $result = [];

        if (!$dataset['model']->getParticipateSale()) {
            /** @var \CDev\Sale\Model\SaleDiscountProduct $saleDiscountProduct */
            foreach ($dataset['model']->getSaleDiscountProducts() as $saleDiscountProduct) {
                $result[] = $saleDiscountProduct->getSaleDiscount()->getName();
            }
        }

        return $result;
    }
}
