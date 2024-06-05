<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductStickers\Logic\Export\Step;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Products extends \XLite\Logic\Export\Step\Products
{
    /**
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        $columns['productStickers'] = [];

        return $columns;
    }

    /**
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return array
     */
    protected function getProductStickersColumnValue(array $dataset, $name, $i)
    {
        $result = [];

        foreach ($dataset['model']->getProductStickers() as $image) {
            $result[] = $image->getName();
        }

        return $result;
    }
}
