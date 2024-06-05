<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\MarketPrice\Logic\Import\Processor;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class Products extends \XLite\Logic\Import\Processor\Products
{
    /**
     * Get messages
     *
     * @return array
     */
    public static function getMessages()
    {
        return parent::getMessages()
        + [
            'PRODUCT-MARKET-PRICE-FMT' => 'Wrong price format',
        ];
    }

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        $columns['marketPrice'] = [];

        return $columns;
    }

    /**
     * Normalize 'marketPrice' value
     *
     * @param mixed $value Value
     *
     * @return float
     */
    protected function normalizeMarketPriceValue($value)
    {
        return $this->normalizeValueAsFloat($value);
    }

    /**
     * Verify 'marketPrice' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyMarketPrice($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsFloat($value)) {
            $this->addWarning('PRODUCT-MARKET-PRICE-FMT', ['column' => $column, 'value' => $value]);
        }
    }

    /**
     * Import 'marketPrice' value
     *
     * @param \XLite\Model\Product $model  Product
     * @param string               $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importMarketPriceColumn(\XLite\Model\Product $model, $value, array $column)
    {
        if ($value) {
            $model->setMarketPrice(floatval($value));
        }
    }
}
