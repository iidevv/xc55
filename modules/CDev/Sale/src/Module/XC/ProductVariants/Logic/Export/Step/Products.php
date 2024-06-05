<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\Module\XC\ProductVariants\Logic\Export\Step;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("XC\ProductVariants")
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

        if ($this->generator->getOptions()->attrs !== 'none') {
            $columns += [
                static::VARIANT_PREFIX . 'Sale' => [static::COLUMN_MULTIPLE => true],
            ];
        }

        return $columns;
    }

    /**
     * Get column value for 'variantSale' column
     *
     * @param array $dataset Dataset
     * @param string $name Column name
     * @param integer $i Subcolumn index
     *
     * @return string
     */
    protected function getVariantSaleColumnValue(array $dataset, $name, $i)
    {
        $result = '';

        if (!empty($dataset['variant']) && !$this->getColumnValueByName($dataset['variant'], 'defaultSale')) {
            $result = $this->getColumnValueByName($dataset['variant'], 'salePriceValue');
            if ($dataset['variant']->getDiscountType() == \XLite\Model\Product::SALE_DISCOUNT_TYPE_PERCENT) {
                $result .= '%';
            }
        }

        return $result;
    }
}
