<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GoogleFeed\Logic\Feed\Step;

use XCart\Extender\Mapping\Extender;

/**
 * Products step
 *
 * @Extender\Mixin
 * @Extender\Depend("CDev\Sale")
 */
class ProductsSale extends \XC\GoogleFeed\Logic\Feed\Step\Products
{
    /**
     * @param \CDev\Sale\Model\Product $model
     * @return string
     */
    protected function getPrice(\XLite\Model\Product $model)
    {
        if ($model->getParticipateSale()) {
            $currency = \XLite::getInstance()->getCurrency();
            $parts = $currency->formatParts($model->getDisplayPriceBeforeSale());
            unset($parts['prefix'], $parts['suffix'], $parts['sign']);
            $parts['code'] = ' ' . strtoupper($currency->getCode());

            return implode('', $parts);
        }

        return parent::getPrice($model);
    }

    /**
     * @param \XLite\Model\Product $model
     * @return array
     */
    protected function getProductRecord(\XLite\Model\Product $model)
    {
        $result = parent::getProductRecord($model);

        if ($model->getParticipateSale()) {
            $currency = \XLite::getInstance()->getCurrency();
            $parts = $currency->formatParts($model->getDisplayPrice());
            unset($parts['prefix'], $parts['suffix'], $parts['sign']);
            $parts['code'] = ' ' . strtoupper($currency->getCode());

            $result['g:sale_price'] = implode('', $parts);
        }

        return $result;
    }
}
