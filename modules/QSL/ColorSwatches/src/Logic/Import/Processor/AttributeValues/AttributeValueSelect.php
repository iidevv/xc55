<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ColorSwatches\Logic\Import\Processor\AttributeValues;

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
        $list['swatch'] = [];

        return $list;
    }

    /**
     * Get messages
     *
     * @return array
     */
    public static function getMessages()
    {
        return parent::getMessages()
            + [
                'ATTRS-SWATCH-FMT' => 'Wrong Swatch value',
            ];
    }

    /**
     * Verify 'swatch' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifySwatch($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value)) {
            /** @var \QSL\ColorSwatches\Model\Repo\Swatch $repo */
            $repo = \XLite\Core\Database::getRepo('QSL\ColorSwatches\Model\Swatch');
            if (!$repo->hasSwatch($value)) {
                $this->addError('ATTRS-SWATCH-FMT', ['column' => $column, 'value' => $value]);
            }
        }
    }

    /**
     * Normalize 'swatch' value
     *
     * @param mixed $value Value
     *
     * @return string
     */
    protected function normalizeSwatchValue($value)
    {
        return $this->normalizeValueAsString($value);
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
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsNull($value)) {

            /** @var \QSL\ColorSwatches\Model\Repo\Swatch $repo */
            $repo = \XLite\Core\Database::getRepo('QSL\ColorSwatches\Model\Swatch');
            $model->setSwatch($repo->findOneByName($value));
        } elseif ($this->verifyValueAsNull($value) && $model->getSwatch()) {
            \XLite\Core\Database::getEM()->remove($model->getSwatch());
            $model->setSwatch(null);
        }
    }
}
