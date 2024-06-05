<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\Logic;

use XCart\Extender\Mapping\Extender;

/**
 * Attribute surcharges
 * @Extender\Mixin
 */
class AttributeSurcharge extends \XLite\Logic\AttributeSurcharge
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
        return parent::isApply($model, $property, $behaviors, $purpose)
            || $model instanceof \XC\ProductVariants\Model\ProductVariant;
    }

    /**
     * Return attribute values
     *
     * @param \XLite\Model\AEntity $model Model
     *
     * @return array
     */
    protected static function getAttributeValues(\XLite\Model\AEntity $model)
    {
        return $model instanceof \XC\ProductVariants\Model\ProductVariant
            ? $model->getProduct()->getAttrValues()
            : parent::getAttributeValues($model);
    }
}
