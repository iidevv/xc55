<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\UpdateInventory\Logic\Import\Processor;

/**
 * Inventory
 */
class Inventory extends \XLite\Logic\Import\Processor\Products
{
    /**
     * Get title
     *
     * @return string
     */
    public static function getTitle()
    {
        return static::t('Products updated');
    }

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'sku' => [
                static::COLUMN_IS_KEY  => true,
                static::COLUMN_LENGTH  => 32,
            ],
            'qty' => [],
        ];
    }

    // {{{ Verification

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
                'NO-PRODUCT-FOUND'        => 'Product with SKU "{{value}}" not found',
                'PRODUCT-STOCK-LEVEL-FMT' => 'Wrong product quantity value format ("{{value}}")',
            ]
        );
    }

    /**
     * Return empty array to prevent displaying of the inventory keys in the 'Import mode' tooltip
     *
     * @return array
     */
    public function getAvailableEntityKeys()
    {
        return [];
    }

    /**
     * Add warning 'No entity found' to the log
     *
     * @return void
     */
    protected function addNoEntityFoundMessage($data = [])
    {
        $this->addWarning(
            'NO-PRODUCT-FOUND',
            [
                'column' => [static::COLUMN_NAME => 'sku'],
                'value' => $data['sku'] ?? ''
            ]
        );
    }

    protected function verifySku($value, array $column)
    {
    }

    /**
     * Verify 'QTY' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyQty($value, array $column)
    {
        parent::verifyStockLevelWithModifier($value, $column);
    }

    // }}}

    // {{{ Import

    /**
     * Import 'Qty' value
     *
     * @param object $model  Product
     * @param mixed  $value  Value
     * @param array  $column Column info
     *
     * @return void
     */
    protected function importQtyColumn($model, $value, array $column)
    {
        parent::importStockLevelWithModifier($model, $value, $column);
    }

    // }}}
}
