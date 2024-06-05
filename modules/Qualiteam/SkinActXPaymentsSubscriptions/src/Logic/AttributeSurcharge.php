<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\Logic;

use XCart\Extender\Mapping\Extender;
use XLite\Model\AEntity;
use XLite\Model\OrderItem;

/**
 * Net price modifier: add attribute surcharge
 *
 * @Extender\Mixin
 */
class AttributeSurcharge extends \XLite\Logic\AttributeSurcharge
{
    /**
     * Check modifier - apply or not
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
        if (
            $model instanceof OrderItem
            && $model->getOrder()
            && $model->getOrder()->isSubscriptionPayment()
        ) {
            $result = false;
        } else {
            $result = parent::isApply($model, $property, $behaviors, $purpose);
        }

        return $result;
    }
}
