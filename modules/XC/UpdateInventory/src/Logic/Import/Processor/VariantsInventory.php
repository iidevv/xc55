<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\UpdateInventory\Logic\Import\Processor;

use XCart\Extender\Mapping\Extender;

/**
 * ProductVariant inventory
 *
 * @Extender\Mixin
 * @Extender\Depend("XC\ProductVariants")
 */
class VariantsInventory extends \XC\UpdateInventory\Logic\Import\Processor\Inventory
{
    /**
     * Get title
     *
     * @return string
     */
    public static function getTitle()
    {
        return static::t('Products and product variants updated');
    }

    /**
     * Get messages
     *
     * @return array
     */
    public static function getMessages()
    {
        return array_merge(
            parent::getMessages(),
            [
                'NO-PRODUCT-FOUND' => 'Product or product variant with SKU "{{value}}" not found',
            ]
        );
    }

    /**
     * Detect model
     *
     * @param array $data Data
     *
     * @return \XLite\Model\AEntity
     */
    protected function detectModel(array $data)
    {
        $model = parent::detectModel($data);

        if (!$model) {
            // Product not found - search for variant
            $conditions = $this->assembleModelConditions($data);

            $model = $conditions
                ? \XLite\Core\Database::getRepo('XC\ProductVariants\Model\ProductVariant')
                    ->findOneByImportConditions($conditions)
                : null;
        }

        return $model;
    }

    // {{{ Import

    /**
     * Import 'Qty' value
     *
     * @param opbect $model  Product
     * @param mixed  $value  Value
     * @param array  $column Column info
     *
     * @return void
     */
    protected function importQtyColumn($model, $value, array $column)
    {
        if ($model instanceof \XC\ProductVariants\Model\ProductVariant) {
            // Update product variant model
            if (
                !$this->verifyValueAsEmpty($value)
                && $this->verifyValueAsUinteger($value)
            ) {
                $model->setAmount($this->normalizeValueAsUinteger($value));
                $model->setDefaultAmount(false);
            } elseif ($this->verifyValueAsModifier($value)) {
                $value = $this->normalizeValueAsModifier($value);
                $newAmount = $model->getAmount() + $value;

                $model->setAmount(max(0, $newAmount));
                $model->setDefaultAmount(false);
            } elseif ($this->verifyValueAsEmpty($value)) {
                $model->setAmount(0);
                $model->setDefaultAmount(true);
            }
        } else {
            parent::importQtyColumn($model, $value, $column);
        }
    }

    // }}}
}
