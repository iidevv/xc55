<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductTags\Logic\Export\Step;

use XCart\Extender\Mapping\Extender;

/**
 * Products
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

        $columns['tags'] = [];

        return $columns;
    }

    /**
     * Get column value for 'marketPrice' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getTagsColumnValue(array $dataset, $name, $i)
    {
        $result = [];

        foreach ($dataset['model']->getTags() as $tag) {
            $result[] = $tag->getName();
        }

        return $result;
    }
}
