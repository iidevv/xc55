<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\Logic;

/**
 * Net price modificator
 */
class MoneyModificator extends \XLite\Logic\ALogic
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
        return self::getObject($model) instanceof \XLite\Model\Product
            && self::getObject($model)->getParticipateSale()
            && static::isApplyForWholesalePrices($model);
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
        return $model instanceof \XLite\Model\Product ? $model : $model->getProduct();
    }

    /**
     * @param \XLite\Model\AEntity $model
     * @return bool
     */
    protected static function isApplyForWholesalePrices(\XLite\Model\AEntity $model)
    {
        return true;
    }
}
