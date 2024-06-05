<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\Logic;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("XC\ProductVariants")
 */
class SaleDiscountVariants extends \CDev\Sale\Logic\SaleDiscount
{
    /**
     * Check modificator - apply or not
     *
     * @param \XLite\Model\AEntity $model     Model
     * @param string               $property  Model's property
     * @param array                $behaviors Behaviors
     * @param string               $purpose   Purpose
     *
     * @return boolean
     */
    public static function isApply(\XLite\Model\AEntity $model, $property, array $behaviors, $purpose)
    {
        $result = parent::isApply($model, $property, $behaviors, $purpose);

        $object = self::getObject($model);

        if ($result && $object instanceof \XC\ProductVariants\Model\ProductVariant) {
            return $object->getDefaultSale();
        }

        return $result;
    }

    /**
     * Modify money
     *
     * @param \XLite\Model\AEntity $model Model
     *
     * @return \XLite\Model\AEntity
     */
    protected static function getObject(\XLite\Model\AEntity $model)
    {
        $object = $model;
        if ($object instanceof \XLite\Model\OrderItem) {
            $object = $object->getVariant();
        }

        if (is_a($object, '\CDev\Wholesale\Model\ProductVariantWholesalePrice')) {
            $object = $object->getProductVariant();
        }

        if ($object instanceof \XC\ProductVariants\Model\ProductVariant) {
            return $object;
        }

        return parent::getObject($model);
    }
}
