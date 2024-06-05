<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\VolumeDiscounts\API\Endpoint\VolumeDiscount\Transformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use CDev\VolumeDiscounts\API\Endpoint\VolumeDiscount\DTO\VolumeDiscountOutput as OutputDTO;
use CDev\VolumeDiscounts\Model\VolumeDiscount as Model;
use DateTimeImmutable;
use Exception;
use XLite\API\SubEntityOutputTransformer\SubEntityIdCollectionOutputTransformerInterface;
use XLite\API\SubEntityOutputTransformer\SubEntityIdOutputTransformerInterface;

class OutputTransformer implements DataTransformerInterface, OutputTransformerInterface
{
    protected SubEntityIdOutputTransformerInterface $membershipIdTransformer;

    protected SubEntityIdCollectionOutputTransformerInterface $zonesIdCollectionOutputTransformer;

    public function __construct(
        SubEntityIdOutputTransformerInterface $membershipIdTransformer,
        SubEntityIdCollectionOutputTransformerInterface $zonesIdCollectionOutputTransformer
    ) {
        $this->membershipIdTransformer = $membershipIdTransformer;
        $this->zonesIdCollectionOutputTransformer = $zonesIdCollectionOutputTransformer;
    }

    /**
     * @param Model $object
     *
     * @throws Exception
     */
    public function transform($object, string $to, array $context = []): OutputDTO
    {
        $dto = new OutputDTO();
        $dto->id = $object->getId();
        $dto->value = $object->getValue();
        $dto->type = $object->getType();
        $dto->subtotal_range_begin = $object->getSubtotalRangeBegin();
        $dto->membership = $this->membershipIdTransformer->transform($object->getMembership());
        $dto->zones = $this->zonesIdCollectionOutputTransformer->transform($object->getZones());
        $dto->date_range_begin = $object->getDateRangeBegin()
            ? new DateTimeImmutable('@' . $object->getDateRangeBegin())
            : null;
        $dto->date_range_end = $object->getDateRangeEnd()
            ? new DateTimeImmutable('@' . $object->getDateRangeEnd())
            : null;

        return $dto;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $to === OutputDTO::class && $data instanceof Model;
    }
}
