<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\NotFinishedOrders;

abstract class Main extends \XLite\Module\AModule
{
    /**
     * Constants: Create NFO modes
     */
    public const NFO_MODE_ON_FAILURE = 'onFailure';
    public const NFO_MODE_ON_PLACE   = 'onPlaceOrder';

    /**
     * Return true if NFO must be created on payment failure
     *
     * @return boolean
     */
    public static function isCreateOnFailure()
    {
        return \XLite\Core\Config::getInstance()->XC->NotFinishedOrders->create_nfo_mode == static::NFO_MODE_ON_FAILURE;
    }

    /**
     * Return true if NFO must be created on place order
     *
     * @return boolean
     */
    public static function isCreateOnPlaceOrder()
    {
        return \XLite\Core\Config::getInstance()->XC->NotFinishedOrders->create_nfo_mode == static::NFO_MODE_ON_PLACE;
    }
}
