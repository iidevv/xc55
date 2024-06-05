<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActColorSwatchesFeature\Logic\Export\Step\AttributeValues;

use XCart\Extender\Mapping\Extender as Extender;

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
        $columns['shipdate'] = [static::COLUMN_GETTER => 'getShipdateColumnValue'];

        return $columns;
    }

    /**
     * Get column value for 'shipdate' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getShipdateColumnValue(array $dataset, $name, $i)
    {
        /** @var \Qualiteam\SkinActColorSwatchesFeature\Model\AttributeValue\AttributeValueSelect $swatch */
        $swatch = $dataset['model'];

        return $swatch ? $swatch->getShipdate() : '';
    }
}