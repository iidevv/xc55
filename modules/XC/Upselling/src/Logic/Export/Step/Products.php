<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Upselling\Logic\Export\Step;

use XCart\Extender\Mapping\Extender;

/**
 * Products
 * @Extender\Mixin
 */
class Products extends \XLite\Logic\Export\Step\Products
{
    // {{{ Columns

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        $columns += [
            'relatedProducts' => [],
        ];

        return $columns;
    }

    /**
     * Get column value for 'relatedProducts' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getRelatedProductsColumnValue(array $dataset, $name, $i)
    {
        $result = [];

        $relProducts = \XLite\Core\Database::getRepo('XC\Upselling\Model\UpsellingProduct')
            ->getUpsellingProducts($dataset['model']->getProductId());

        foreach ($relProducts as $rel) {
            if ($rel->getProduct()) {
                $result[] = $rel->getProduct()->getSku();
            }
        }

        return $result;
    }

    // }}}
}
