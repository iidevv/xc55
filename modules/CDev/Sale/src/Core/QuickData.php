<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\Core;

use XCart\Extender\Mapping\Extender;
use CDev\Sale\View\FormField\Select\CombineDiscounts;

/**
 * @Extender\Mixin
 */
class QuickData extends \XLite\Core\QuickData
{
    /**
     * @param \XLite\Model\Product $product
     * @param $membership
     * @param $zone
     * @return float
     */
    protected function getQuickDataPrice(\XLite\Model\Product $product, $membership, $zone)
    {
        $quickDataPrice = parent::getQuickDataPrice($product, $membership, $zone);

        if (!$product->getParticipateSale()) {
            $saleDiscounts = \XLite\Core\Database::getRepo('CDev\Sale\Model\SaleDiscount')
                ->findAllActiveForCalculate();


            switch (\XLite\Core\Config::getInstance()->CDev->Sale->way_to_combine_discounts) {
                case CombineDiscounts::TYPE_APPLY_MAX:
                    foreach ($saleDiscounts as $saleDiscount) {
                        if ($this->isSaleDiscountApplicable($saleDiscount, $product, $membership)) {
                            $quickDataPrice = $quickDataPrice * (1 - $saleDiscount->getValue() / 100);
                            break;
                        }
                    }
                    break;
                case CombineDiscounts::TYPE_APPLY_MIN:
                    foreach (array_reverse($saleDiscounts) as $saleDiscount) {
                        if ($this->isSaleDiscountApplicable($saleDiscount, $product, $membership)) {
                            $quickDataPrice = $quickDataPrice * (1 - $saleDiscount->getValue() / 100);
                            break;
                        }
                    }
                    break;
                case CombineDiscounts::TYPE_SUM_UP:
                    $percentSum = 0;
                    foreach ($saleDiscounts as $saleDiscount) {
                        if ($this->isSaleDiscountApplicable($saleDiscount, $product, $membership)) {
                            $percentSum += $saleDiscount->getValue();
                        }
                    }
                    $quickDataPrice = $quickDataPrice * (1 - min(100, $percentSum) / 100);
                    break;
            }
        }

        return $quickDataPrice;
    }

    /**
     * @param \CDev\Sale\Model\SaleDiscount $saleDiscount
     * @param \XLite\Model\Product $product
     * @param $membership
     * @return bool
     */
    protected function isSaleDiscountApplicable(\CDev\Sale\Model\SaleDiscount $saleDiscount, \XLite\Model\Product $product, $membership)
    {
        if (!$saleDiscount->isApplicableForProduct($product)) {
            return false;
        }

        if (
            $saleDiscount->getMemberships()->count()
            && !$saleDiscount->getMemberships()->contains($membership)
        ) {
            return false;
        }

        return true;
    }
}
