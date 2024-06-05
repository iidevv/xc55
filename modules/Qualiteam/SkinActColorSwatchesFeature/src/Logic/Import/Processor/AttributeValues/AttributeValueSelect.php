<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActColorSwatchesFeature\Logic\Import\Processor\AttributeValues;

use XCart\Extender\Mapping\Extender;

/**
 * Product attributes values import processor
 * @Extender\Mixin
 */
class AttributeValueSelect extends \XLite\Logic\Import\Processor\AttributeValues\AttributeValueSelect
{
    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $list = parent::defineColumns();
        $list['shipdate'] = [];

        return $list;
    }

    /**
     * Normalize 'swatch' value
     *
     * @param mixed $value Value
     *
     * @return string
     */
    protected function normalizeShipdateValue($value)
    {
        return $this->normalizeValueAsString($value);
    }

    /**
     * Import 'shipdate' value
     *
     * @param \XLite\Model\AttributeValue\AAttributeValue $model  Shipdate
     * @param string                                      $value  Value
     * @param array                                       $column Column info
     */
    protected function importShipdateColumn(\XLite\Model\AttributeValue\AAttributeValue $model, $value, array $column)
    {
        $model->setShipdate($value);
    }

    /**
     * Import 'swatch' value
     *
     * @param \XLite\Model\AttributeValue\AAttributeValue $model  Swatch
     * @param string                                      $value  Value
     * @param array                                       $column Column info
     */
    protected function importSwatchColumn(\XLite\Model\AttributeValue\AAttributeValue $model, $value, array $column)
    {
        if (!$this->verifyValueAsNull($value)) {
            /** @var \QSL\ColorSwatches\Model\Repo\Swatch $repo */
            $repo = \XLite\Core\Database::getRepo('QSL\ColorSwatches\Model\Swatch');
            $model->setSwatch($repo->findOneByName($value));
        } elseif ($this->verifyValueAsNull($value) && $model->getSwatch()) {
            \XLite\Core\Database::getEM()->remove($model->getSwatch());
            $model->setSwatch(null);
        }
    }
}
