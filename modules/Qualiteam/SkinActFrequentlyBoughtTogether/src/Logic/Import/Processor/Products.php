<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFrequentlyBoughtTogether\Logic\Import\Processor;

use Qualiteam\SkinActFrequentlyBoughtTogether\Traits\FreqBoughtTogetherTrait;
use XCart\Extender\Mapping\Extender;
use XLite\Model\Product;

/**
 * @Extender\Mixin
 */
abstract class Products extends \XLite\Logic\Import\Processor\Products
{
    use FreqBoughtTogetherTrait;

    public static function getMessages()
    {
        $message[static::getMessageCode()] = static::getMessageText();

        return parent::getMessages() + $message;
    }

    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        $columns[$this->getExcludeFreqBoughtTogetherParamName()] = [];

        return $columns;
    }

    /**
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyIsExcludeFreqBoughtTogether($value, array $column)
    {
        if (!$this->verifyValueAsBoolean($value)) {
            $this->addWarning(static::getMessageCode(), ['column' => $column, 'value' => $value]);
        }
    }

    /**
     * @param \XLite\Model\Product $model  Product
     * @param mixed                $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importIsExcludeFreqBoughtTogetherColumn(Product $model, $value, array $column)
    {
        $model->setExcludeFreqBoughtTogether($this->normalizeValueAsBoolean($value));
    }

    protected static function getMessageCode()
    {
        return 'PRODUCT-EXCLUDE-FREQ-FMT';
    }

    protected static function getMessageText()
    {
        return static::t('SkinActFrequentlyBoughtTogether wrong exclude frequently bought together column format');
    }
}