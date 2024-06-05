<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\Order\Transformer\TrackingNumber;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use DateTimeImmutable;
use Exception;
use XLite\API\Endpoint\Order\DTO\TrackingNumber\OrderTrackingNumberOutput as OutputDTO;
use XLite\Model\OrderTrackingNumber;

class OutputTransformer implements DataTransformerInterface, OutputTransformerInterface
{
    /**
     * @param OrderTrackingNumber $object
     *
     * @throws Exception
     */
    public function transform($object, string $to, array $context = []): OutputDTO
    {
        $dto = new OutputDTO();
        $dto->id = $object->getTrackingId();
        $dto->date = new DateTimeImmutable('@' . $object->getCreationDate());
        $dto->value = $object->getValue();

        return $dto;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $to === OutputDTO::class && $data instanceof OrderTrackingNumber;
    }
}
