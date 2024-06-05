<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\Logic;

use XLite\Logic\ALogic;
use XLite\Model\AEntity;
use XLite\Model\OrderItem;
use XLite\Model\Product;

/**
 * Subscription Fee
 */
class SubscriptionFee extends ALogic
{
    /**
     * Check modifier - apply or not
     *
     * @param AEntity $model Model
     * @param string $property Model's property
     * @param array $behaviors Behaviors
     * @param string $purpose Purpose
     *
     * @return boolean
     */
    public static function isApply(AEntity $model, $property, array $behaviors, $purpose)
    {
        return (
                $model instanceof Product
                && $model->hasSubscriptionPlan()
            )
            || (
                $model instanceof OrderItem
                && $model->isSubscription()
                && $model->getOrder()
                && !$model->getOrder()->isSubscriptionPayment()
            );
    }

    /**
     * Modify money
     *
     * @param float $value Value
     * @param AEntity $model Model
     * @param string $property Model's property
     * @param array $behaviors Behaviors
     * @param string $purpose Purpose
     *
     * @return float
     */
    public static function modifyMoney($value, AEntity $model, $property, array $behaviors, $purpose)
    {
        return $value + $model->getNetFeePrice();
    }
}
