<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\USPS\API\Endpoint\Order\Transformer;

use XCart\Extender\Mapping\Extender;
use XLite\API\Endpoint\Order\DTO\BaseOutput;
use CDev\USPS\API\Endpoint\Order\DTO\Output as ModuleOutputDTO;
use CDev\USPS\API\Endpoint\Order\Transformer\Shipment\OutputTransformerInterface;

/**
 * @Extender\Mixin
 */
class OutputTransformerAbstract extends \XLite\API\Endpoint\Order\Transformer\OutputTransformerAbstract
{
    protected OutputTransformerInterface $shipmentTransformer;

    /**
     * @required
     */
    public function setShipmentTransformer(OutputTransformerInterface $shipmentTransformer): void
    {
        $this->shipmentTransformer = $shipmentTransformer;
    }

    protected function basicTransform(BaseOutput $dto, $object, string $to, array $context = []): BaseOutput
    {
        /** @var ModuleOutputDTO $dto */
        $dto = parent::basicTransform($dto, $object, $to, $context);

        $dto->usps_shipments = [];
        foreach ($object->getUspsShipment() as $shipment) {
            $dto->usps_shipments[] = $this->shipmentTransformer->transform($shipment, $to, $context);
        }

        return $dto;
    }
}
