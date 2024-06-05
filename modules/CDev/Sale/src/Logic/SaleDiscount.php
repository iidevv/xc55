<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\Logic;

use CDev\Sale\View\FormField\Select\CombineDiscounts;

/**
 * Net price modificator: price with sale discount
 */
class SaleDiscount extends \XLite\Logic\ALogic
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
            && !self::getObject($model)->getParticipateSale();
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
        $saleValue = static::getSaleDiscountValue($model, $value);

        $result = $value - $saleValue;

        $currency = \XLite::getInstance()->getCurrency();

        if ($currency) {
            $result = \XLite::getInstance()->getCurrency()->roundValue($result);
        }

        return $result;
    }

    protected static function getSaleDiscountValue($model, $value)
    {
        $saleDiscounts = \XLite\Core\Database::getRepo('CDev\Sale\Model\SaleDiscount')
            ->findAllActiveForCalculate();

        $result = 0;

        switch (\XLite\Core\Config::getInstance()->CDev->Sale->way_to_combine_discounts) {
            case CombineDiscounts::TYPE_APPLY_MAX:
                foreach ($saleDiscounts as $saleDiscount) {
                    if (static::isSaleDiscountApplicable($saleDiscount, $model)) {
                        $result = $value * $saleDiscount->getValue() / 100;
                        break;
                    }
                }
                break;
            case CombineDiscounts::TYPE_APPLY_MIN:
                foreach (array_reverse($saleDiscounts) as $saleDiscount) {
                    if (static::isSaleDiscountApplicable($saleDiscount, $model)) {
                        $result = $value * $saleDiscount->getValue() / 100;
                        break;
                    }
                }
                break;
            case CombineDiscounts::TYPE_SUM_UP:
                $percentSum = 0;
                foreach ($saleDiscounts as $saleDiscount) {
                    if (static::isSaleDiscountApplicable($saleDiscount, $model)) {
                        $percentSum += $saleDiscount->getValue();
                    }
                }
                $result = $value * min(100, $percentSum) / 100;
                break;
        }

        return $result;
    }

    protected static function isSaleDiscountApplicable(\CDev\Sale\Model\SaleDiscount $saleDiscount, $model)
    {
        $object = static::getObject($model);
        if (!$object instanceof \XLite\Model\Product) {
            $object = $object->getProduct();
        }

        if (!$saleDiscount->isApplicableForProduct($object)) {
            return false;
        }

        $controller = \XLite::getController();
        $profile = null;

        if ($controller instanceof \XLite\Controller\Customer\ACustomer) {
            $profile = $controller->getCart(true)->getProfile()
                ?: \XLite\Core\Auth::getInstance()->getProfile();
        }

        if (!$profile) {
            $profile = new \XLite\Model\Profile();
        }

        if (!$saleDiscount->isApplicableForProfile($profile)) {
            return false;
        }

        return true;
    }
}
