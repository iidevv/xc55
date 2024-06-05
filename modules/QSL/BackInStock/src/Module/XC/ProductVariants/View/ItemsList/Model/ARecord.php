<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\Module\XC\ProductVariants\View\ItemsList\Model;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("XC\ProductVariants")
 */
abstract class ARecord extends \QSL\BackInStock\View\ItemsList\Model\ARecord
{
    /**
     * Preprocess product value
     *
     * @param \XLite\Model\Product                       $value Value
     * @param array                                      $column Column data
     * @param \QSL\BackInStock\Model\ARecord $entity Entity
     *
     * @return string
     */
    protected function preprocessProduct(\XLite\Model\Product $value, array $column, \QSL\BackInStock\Model\ARecord $entity)
    {
        return $entity->getExtendedRecordProductName();
    }
}
