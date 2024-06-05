<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\USPS\API\Endpoint\Order\Transformer\Shipment;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use DateTimeImmutable;
use CDev\USPS\API\Endpoint\Order\DTO\Shipment\OrderUSPSShipmentOutput as OutputDTO;
use CDev\USPS\Model\Shipment;

class OutputTransformer implements DataTransformerInterface, OutputTransformerInterface
{
    /**
     * @param Shipment $object
     */
    public function transform($object, string $to, array $context = []): OutputDTO
    {
        $dto = new OutputDTO();
        $dto->id = $object->getId();
        $dto->tracking_data = $object->getTrackingData();
        $dto->request_data = $object->getRequestData();
        $dto->response_data = $object->getResponseData();
        $dto->print_date = new DateTimeImmutable('@' . $object->getPrintDate());
        $dto->price = $object->getPrice();
        $dto->shipment_id = $object->getShipmentId();
        $dto->tracking_number = $object->getTrackingNumber();
        $dto->tracking_data = $object->getTrackingData();
        $dto->label_url = $object->getLabelURL();
        $dto->label_content = $object->getLabelContent();

        return $dto;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $to === OutputDTO::class && $data instanceof Shipment;
    }
}
