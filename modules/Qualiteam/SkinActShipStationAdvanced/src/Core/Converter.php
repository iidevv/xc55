<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActShipStationAdvanced\Core;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Converter extends \XLite\Core\Converter
{
    public static function convertLbsToOz(float $weight): float
    {
        return $weight * 16;
    }
}