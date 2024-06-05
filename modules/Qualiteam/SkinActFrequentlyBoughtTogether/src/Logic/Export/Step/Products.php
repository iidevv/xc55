<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFrequentlyBoughtTogether\Logic\Export\Step;

use Qualiteam\SkinActFrequentlyBoughtTogether\Traits\FreqBoughtTogetherTrait;
use XCart\Extender\Mapping\Extender;

/**
 * Products
 * @Extender\Mixin
 */
abstract class Products extends \XLite\Logic\Export\Step\Products
{
    use FreqBoughtTogetherTrait;

    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        $columns[$this->getExcludeFreqBoughtTogetherParamName()] = [];

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
    protected function getIsExcludeFreqBoughtTogetherColumnValue(array $dataset, $name, $i)
    {
        return $dataset['model']->getExcludeFreqBoughtTogether() ?? false;
    }
}