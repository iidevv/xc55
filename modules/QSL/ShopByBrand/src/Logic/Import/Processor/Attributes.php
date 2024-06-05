<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\Logic\Import\Processor;

use XCart\Extender\Mapping\Extender;

/**
 * Attributes import processor
 * @Extender\Mixin
 */
class Attributes extends \XLite\Logic\Import\Processor\Attributes
{
    /**
     * Import 'options' value
     *
     * @param \XLite\Model\Attribute $model  Attribute
     * @param array                  $value  Value
     * @param array                  $column Column info
     */
    protected function importOptionsColumn(\XLite\Model\Attribute $model, array $value, array $column)
    {
        parent::importOptionsColumn($model, $value, $column);

        if ($model->isBrandAttribute()) {
            // Update associated brands
            foreach ($model->getAttributeOptions() as $option) {
                $brand = \XLite\Core\Database::getRepo('QSL\ShopByBrand\Model\Brand')
                    ->findOneByOption($option);

                if (!$brand) {
                    $option->createAssociatedBrand();
                }
            }
        }
    }
}
