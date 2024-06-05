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
class Categories extends \XLite\Logic\Export\Step\Categories
{
    /**
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        $columns['categoryStickers'] = [];
        $columns['isStickersIncludedSubcategories'] = [];

        return $columns;
    }

    /**
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return array
     */
    protected function getCategoryStickersColumnValue(array $dataset, $name, $i)
    {
        $result = [];

        foreach ($dataset['model']->getCategoryStickers() as $image) {
            $result[] = $image->getName();
        }

        return $result;
    }

    /**
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return boolean
     */
    protected function getIsStickersIncludedSubcategoriesColumnValue(array $dataset, $name, $i)
    {
        return $this->getColumnValueByName($dataset['model'], 'isStickersIncludedSubcategories');
    }
}
