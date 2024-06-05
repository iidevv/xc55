<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActShipStationAdvanced;

use XLite\Core\Config;

class Main extends \XLite\Module\AModule
{
    public static function isDeveloperMode(): bool
    {
        return Config::getInstance()->ShipStation->Api->ssa_api_developer_mode;
    }

    public static function getDeveloperModeProductSkus(): array
    {
        $skus = Config::getInstance()->ShipStation->Api->ssa_api_developer_mode_skus;
        $result = explode(',', $skus);

        foreach ($result as $key => $sku) {
            $result[$key] = trim($sku);
        }

        return $result;
    }
}