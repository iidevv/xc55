<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ColorSwatches\Logic\Export\Step\AttributeValues;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class AAttributeValues extends \XLite\Logic\Export\Step\AttributeValues\AAttributeValues
{
    /**
     * @inheritdoc
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();
        $columns['swatch'] = [static::COLUMN_GETTER => 'getSwatchColorColumnValue'];

        return $columns;
    }

    /**
     * Get column value for 'swatchColor' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getSwatchColorColumnValue(array $dataset, $name, $i)
    {
        /** @var \QSL\ColorSwatches\Model\Swatch $swatch */
        $swatch = $dataset['model']->getSwatch();

        return $swatch ? $swatch->getName() : '';
    }
}
