<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActPinterestFeed\Logic\Export\Step;

use XCart\Extender\Mapping\Extender;

/**
 * Products
 * @Extender\Mixin
 */
abstract class Products extends \XLite\Logic\Export\Step\Products
{
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        $columns['pinterestCategory'] = [];

        return $columns;
    }

    /**
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getPinterestCategoryColumnValue(array $dataset, $name, $i)
    {
        $c = $dataset['model']->getPinterestCategory();

        return $c ? $c->getName() : '';
    }
}