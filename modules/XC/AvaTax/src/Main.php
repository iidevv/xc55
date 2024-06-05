<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\AvaTax;

use XLite\Core\Config;

abstract class Main extends \XLite\Module\AModule
{
    /**
     * @return boolean
     */
    public static function hasGdprRelatedActivity()
    {
        return true;
    }

    public static function isColoradoRetailDeliveryFeeCollectionEnabled(): bool
    {
        return (bool)Config::getInstance()->XC->AvaTax->collect_retail_delivery_fee;
    }
}
