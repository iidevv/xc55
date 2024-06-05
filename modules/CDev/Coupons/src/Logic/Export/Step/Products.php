<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Coupons\Logic\Export\Step;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Products extends \XLite\Logic\Export\Step\Products
{
    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        return array_merge(parent::defineColumns(), [
            'couponCodes'   => [],
        ]);
    }

    /**
     * Get 'couponCodes' column value
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return array
     */
    protected function getCouponCodesColumnValue(array $dataset, $name, $i)
    {
        $result = [];

        foreach ($dataset['model']->getCouponProducts() as $couponProduct) {
            $result[] = $couponProduct->getCoupon()->getCode();
        }

        return $result;
    }
}
