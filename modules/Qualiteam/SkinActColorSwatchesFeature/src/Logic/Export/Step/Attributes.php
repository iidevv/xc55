<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActColorSwatchesFeature\Logic\Export\Step;

use XCart\Extender\Mapping\Extender;

/**
 * Products attribute values: select
 * @Extender\Mixin
 */
class Attributes extends \XLite\Logic\Export\Step\Attributes
{
    /**
     * @inheritdoc
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();
        $columns['shipdates'] = [static::COLUMN_GETTER => 'getShipdatesColumnValue'];

        return $columns;
    }

    /**
     * Get column value for 'swatches' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return array
     */
    protected function getShipdatesColumnValue(array $dataset, $name, $i)
    {
        $result = [];
        /** @var \XLite\Model\AttributeOption $option */
        foreach ($dataset['model']->getAttributeOptions() as $i => $option) {
            $result[$i] = $option->getShipdate();
        }

        return $result;
    }
}
