<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\Module\Qualiteam\SkinActShipStationAdvanced\Api;

use XCart\Extender\Mapping\Extender as Extender;
use XLite\InjectLoggerTrait;

/**
 * Class ship station api
 * @Extender\Depend ("Qualiteam\SkinActShipStationAdvanced")
 * @Extender\Mixin
 */
class ShipStationApi extends \Qualiteam\SkinActShipStationAdvanced\Api\ShipStationApi
{
    use InjectLoggerTrait;

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getListOrders(array $params)
    {
        return json_decode(
            $this->getRequest('/orders', $params)->getBody(),
            true
        );
    }
}