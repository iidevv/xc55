<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\Logic;

use XLite\Logic\ALogic;
use XLite\Model\AEntity;
use XLite\Model\AttributeValue\Multiple;
use XLite\Model\OrderItem;
use XLite\Model\OrderItem\AttributeValue;
use XLite\Model\Product;

/**
 * Subscription fee modificator: add attribute surcharge
 */
class SubscriptionFeeModifier extends ALogic
{
    /**
     * Check modificator - apply or not
     *
     * @param AEntity $model     Model
     * @param string               $property  Model's property
     * @param array                $behaviors Behaviors
     * @param string               $purpose   Purpose
     *
     * @return boolean
     */
    public static function isApply(AEntity $model, $property, array $behaviors, $purpose)
    {
        return $model instanceOf OrderItem || $model instanceOf Product;
    }

    /**
     * Modify money
     *
     * @param float                $value     Value
     * @param AEntity $model     Model
     * @param string               $property  Model's property
     * @param array                $behaviors Behaviors
     * @param string               $purpose   Purpose
     *
     * @return void
     */
    public static function modifyMoney($value, AEntity $model, $property, array $behaviors, $purpose)
    {
        foreach (static::getAttributeValues($model) as $attributeValue) {
            if (
                $attributeValue instanceOf AttributeValue
                && $attributeValue->getAttributeValue()
            ) {
                $attributeValue = $attributeValue->getAttributeValue();
            }

            if (
                is_object($attributeValue)
                && $attributeValue instanceof Multiple
            ) {
                $value += $attributeValue->getAbsoluteValue('subscriptionFee');
            }
        }

        return $value;
    }

    /**
     * Return attribute values
     *
     * @param AEntity $model Model
     *
     * @return array
     */
    protected static function getAttributeValues(AEntity $model)
    {
        return $model instanceOf Product
            ? $model->getAttrValues()
            : $model->getAttributeValues();
    }
}
