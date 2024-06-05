<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\Logic;

use XCart\Extender\Mapping\Extender;
use XC\ProductVariants\Model\ProductVariant;

/**
 * @Extender\Mixin
 * @Extender\Depend("XC\ProductVariants")
 */
class MoneyModificatorVariants extends \CDev\Sale\Logic\MoneyModificator
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
        $obj = self::getObject($model);
        if ($obj instanceof ProductVariant) {
            return !$obj->getDefaultSale() && static::isApplyForWholesalePrices($model);
        }

        return parent::isApply($model, $property, $behaviors, $purpose);
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
        $object = static::getVariantObject($model);

        if ($object && !$object->getDefaultSale()) {
            return $object;
        }

        return parent::getObject($model);
    }

    /**
     * Modify money
     *
     * @param \XLite\Model\AEntity $model Model
     *
     * @return \XLite\Model\AEntity
     */
    protected static function getVariantObject(\XLite\Model\AEntity $model)
    {
        $object = $model;
        if ($object instanceof \XLite\Model\OrderItem) {
            $object = $object->getVariant();
        } elseif (is_a($object, '\CDev\Wholesale\Model\ProductVariantWholesalePrice')) {
            $object = $object->getProductVariant();
        }

        return $object instanceof ProductVariant ? $object : null;
    }
}
