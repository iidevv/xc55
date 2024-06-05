<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\USPS\API\Endpoint\Order\Transformer\Shipment;

use CDev\USPS\API\Endpoint\Order\DTO\Shipment\OrderUSPSShipmentOutput as OutputDTO;
use CDev\USPS\Model\Shipment;

interface OutputTransformerInterface
{
    public function transform(Shipment $object, string $to, array $context = []): OutputDTO;

    public function supportsTransformation($data, string $to, array $context = []): bool;
}
