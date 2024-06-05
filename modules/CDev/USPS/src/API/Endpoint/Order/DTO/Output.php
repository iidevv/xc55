<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\USPS\API\Endpoint\Order\DTO;

use XCart\Extender\Mapping\Extender;
use XLite\API\Endpoint\Order\DTO\BaseOutput as ExtendedOutput;
use CDev\USPS\API\Endpoint\Order\DTO\Shipment\OrderUSPSShipmentOutput as ShipmentOutput;

/**
 * @Extender\Mixin
 */
class Output extends ExtendedOutput
{
    /**
     * @var ShipmentOutput[]
     */
    public array $usps_shipments = [];
}
