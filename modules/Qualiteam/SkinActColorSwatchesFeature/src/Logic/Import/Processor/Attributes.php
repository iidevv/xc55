<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActColorSwatchesFeature\Logic\Import\Processor;

use XCart\Extender\Mapping\Extender;

/**
 * Attributes import processor
 * @Extender\Mixin
 */
class Attributes extends \XLite\Logic\Import\Processor\Attributes
{
    /**
     * @inheritdoc
     */
    protected function defineColumns()
    {
        $list              = parent::defineColumns();
        $list['shipdates'] = [
            static::COLUMN_IS_MULTIPLE => true,
            static::COLUMN_LENGTH      => 255,
        ];

        return $list;
    }

    /**
     * Import 'shipdates' value
     *
     * @param \XLite\Model\Attribute $model  Attribute
     * @param array                  $value  Value
     * @param array                  $column Column info
     *
     * @return void
     */
    protected function importShipdatesColumn(\XLite\Model\Attribute $model, array $value, array $column)
    {
        foreach ($value as $item) {
            $model->getAttributeOptions()->setShipdate($item);
        }
    }

    /**
     * Import 'swatches' value
     *
     * @param \XLite\Model\Attribute $model  Attribute
     * @param array                  $value  Value
     * @param array                  $column Column info
     *
     * @return void
     */
    protected function importSwatchesColumn(\XLite\Model\Attribute $model, array $value, array $column)
    {
        if ($value) {
            if (!$this->verifyValueAsNull($value)) {
                /** @var \QSL\ColorSwatches\Model\Repo\Swatch $repo */
                $repo = \XLite\Core\Database::getRepo('QSL\ColorSwatches\Model\Swatch');

                foreach ($value as $index => $color) {
                    /** @var \XLite\Model\AttributeOption $option */
                    $option = $model->getAttributeOptions()->get($index);
                    if ($option) {
                        $option->setSwatch($repo->findOneByName($color));
                    }
                }
            } else {

                /** @var \XLite\Model\AttributeOption $option */
                foreach ($model->getAttributeOptions() as $option) {
                    if ($option->getSwatch()) {
                        \XLite\Core\Database::getEM()->remove($option->getSwatch());
                        $option->setSwatch(null);
                    }
                }
            }
        }
    }
}
