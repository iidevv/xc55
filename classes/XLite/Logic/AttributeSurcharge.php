<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic;

/**
 * Net price modificator: add attribute surcharge
 */
class AttributeSurcharge extends \XLite\Logic\ALogic
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
        return $model instanceof \XLite\Model\OrderItem || $model instanceof \XLite\Model\Product;
    }

    /**
     * Modify money
     *
     * @param float                $value     Value
     * @param \XLite\Model\AEntity $model     Model
     * @param string               $property  Model's property
     * @param array                $behaviors Behaviors
     * @param string               $purpose   Purpose
     *
     * @return float
     */
    public static function modifyMoney($value, \XLite\Model\AEntity $model, $property, array $behaviors, $purpose)
    {
        foreach (static::getAttributeValues($model) as $attributeValue) {
            if (
                $attributeValue instanceof \XLite\Model\OrderItem\AttributeValue
                && $attributeValue->getAttributeValue()
            ) {
                $attributeValue = $attributeValue->getAttributeValue();
            }

            if (
                is_object($attributeValue)
                && $attributeValue instanceof \XLite\Model\AttributeValue\Multiple
            ) {
                $value += $attributeValue->getAbsoluteValue('price');
            }
        }

        return $value;
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
        return $model instanceof \XLite\Model\Product
            ? $model->getAttrValues()
            : $model->getAttributeValues();
    }
}
