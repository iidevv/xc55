<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Module;

/**
 * FreeShipping module
 */
class FreeShipping extends \XC\MigrationWizard\Logic\Import\Processor\XCart\Module\AModule
{
    /**
     * Define required modules list
     *
     * @return array
     */
    public static function defineRequiredModules()
    {
        return ['XC\FreeShipping'];
    }

    // {{{ Logic <editor-fold desc="Logic" defaultstate="collapsed">

    protected static function checkTransferableDataPresent()
    {
        $prefix = static::getTablePrefix();

        $isFreeShippingOrderExist = (bool) static::getCellData(
            'SELECT 1'
            . " FROM {$prefix}orders WHERE coupon LIKE '%free_ship% LIMIT 1"
        );

        $isFreeShippingCouponExist = (bool) static::getCellData(
            'SELECT 1'
            . " FROM {$prefix}discount_coupons WHERE coupon_type = 'free_ship' LIMIT 1"
        );

        return $isFreeShippingCouponExist || $isFreeShippingOrderExist;
    }

    // }}} </editor-fold>
}
