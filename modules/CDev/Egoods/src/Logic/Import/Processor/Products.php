<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Egoods\Logic\Import\Processor;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class Products extends \XLite\Logic\Import\Processor\Products
{
    // {{{ Columns

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        $columns['attachmentsPrivate'] = [
            static::COLUMN_IS_MULTIPLE => true,
        ];

        return $columns;
    }

    // }}}

    // {{{ Import

    /**
     * Import 'attachmentsPrivate' value
     *
     * @param \XLite\Model\Product $model  Product
     * @param array                $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importAttachmentsPrivateColumn(\XLite\Model\Product $model, array $value, array $column)
    {
        if ($value) {
            foreach ($value as $index => $val) {
                $attachment = $model->getAttachments()->get($index);
                if ($attachment) {
                    $attachment->setPrivate($this->normalizeValueAsBoolean($val));
                }
            }
        }
    }

    // }}}
}
