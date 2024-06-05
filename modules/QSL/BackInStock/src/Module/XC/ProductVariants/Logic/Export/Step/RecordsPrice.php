<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\Module\XC\ProductVariants\Logic\Export\Step;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("XC\ProductVariants")
 */
class RecordsPrice extends \QSL\BackInStock\Logic\Export\Step\RecordsPrice
{
    /**
     * Get column value for 'name' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getNameColumnValue(array $dataset, $name, $i)
    {
        return $dataset['model']->getExtendedRecordProductName();
    }
}
